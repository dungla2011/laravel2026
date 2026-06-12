# Lệnh	Tác dụng
# python setip.py install	Cài service (Windows/Linux systemd)
# python setip.py remove	Gỡ service
# python setip.py start/stop/status	Quản lý service (Linux)
# python setip.py mac	Lưu MAC hiện tại vào file template (dùng khi tạo VM template)
# Script này được thiết kế chạy tự động khi boot để VPS clone từ template có thể tự nhận IP từ server quản lý qua MAC address.

import subprocess 
import sys
import os
import time
import logging
import platform
import socket
import re
import ssl
import hashlib
from urllib.request import urlopen, Request
from urllib.error import URLError

# Import Windows-specific modules only on Windows
SYSTEM = platform.system()
if SYSTEM == "Windows":
    import win32serviceutil
    import win32service
    import win32event
    import servicemanager

# ============================================================================
# Configuration
# ============================================================================
SERVICE_NAME = "GlxService.2.1"
METADATA_SERVER = "10.1.1.1"
METADATA_URL = f"http://{METADATA_SERVER}/tool/get_ip_glx.php"
METADATA_URL2 = f"https://glx.lad.vn/tool/get_ip_glx.php"
MAX_RETRIES = 5
RETRY_INTERVAL = 5

# Cấu hình đường dẫn log - tùy thuộc vào hệ điều hành
if SYSTEM == "Windows":
    LOG_DIR = r"C:\Windows\Logs"
    if not os.path.exists(LOG_DIR):
        try:
            os.makedirs(LOG_DIR)
        except:
            LOG_DIR = os.path.join(os.environ.get('SYSTEMDRIVE', 'C:'), 'Windows', 'Logs')
            if not os.path.exists(LOG_DIR):
                os.makedirs(LOG_DIR)
else:
    # Linux: /var/log
    LOG_DIR = "/var/log"
    if not os.path.exists(LOG_DIR):
        try:
            os.makedirs(LOG_DIR)
        except:
            LOG_DIR = "/tmp"

LOG_FILE = os.path.join(LOG_DIR, 'glx.log')
MAC_TEMPLATE_FILE = os.path.join(LOG_DIR, 'mac_address_this_template')
PASSWORD_SET_FLAG = os.path.join(LOG_DIR, 'glx_set_pw_done')

# Global variable để track xem có in console hay không
CONSOLE_OUTPUT = False

def setup_logging(console=False):
    """Cấu hình logging"""
    global CONSOLE_OUTPUT
    CONSOLE_OUTPUT = console
    
    # Cấu hình formatter
    formatter = logging.Formatter(
        '%(asctime)s - %(levelname)s - %(message)s',
        datefmt='%Y-%m-%d %H:%M:%S'
    )
    
    # Remove any existing handlers
    logger = logging.getLogger()
    logger.handlers = []
    logger.setLevel(logging.DEBUG)
    
    # File handler
    file_handler = logging.FileHandler(LOG_FILE)
    file_handler.setLevel(logging.DEBUG)
    file_handler.setFormatter(formatter)
    logger.addHandler(file_handler)
    
    # Console handler (nếu console=True)
    if console:
        console_handler = logging.StreamHandler()
        console_handler.setLevel(logging.INFO)
        console_handler.setFormatter(formatter)
        logger.addHandler(console_handler)

# ============================================================================
# Network Configuration Functions
# ============================================================================

def get_all_mac_addresses():
    """Get ALL MAC addresses of all network interfaces"""
    mac_addresses = []

    try:
        if SYSTEM == "Windows":
            result = subprocess.run(
                ["ipconfig", "/all"],
                capture_output=True,
                text=True,
                timeout=5
            )

            for line in result.stdout.split('\n'):
                if 'Physical Address' in line:
                    match = re.search(r':\s*([0-9A-Fa-f\-]{17})', line)
                    if match:
                        mac = match.group(1).strip().upper()
                        if mac not in mac_addresses:
                            mac_addresses.append(mac)
        else:
            sys_net = '/sys/class/net'
            logging.debug(f"Reading MAC from {sys_net}")
            for iface in sorted(os.listdir(sys_net)):
                if iface == 'lo':
                    continue
                mac_file = os.path.join(sys_net, iface, 'address')
                if os.path.exists(mac_file):
                    with open(mac_file) as f:
                        mac = f.read().strip().upper()
                    logging.debug(f"Interface {iface}: {mac}")
                    if mac and mac not in ['00:00:00:00:00:00', '00-00-00-00-00-00'] and mac not in mac_addresses:
                        mac_addresses.append(mac)

        return mac_addresses

    except Exception as e:
        logging.error(f"Error getting all MAC addresses: {e}")
        return mac_addresses


def save_template_mac_addresses():
    """Save all MAC addresses to template file"""
    mac_addresses = get_all_mac_addresses()

    if not mac_addresses:
        print("ERROR: Could not get any MAC addresses")
        return False

    # Bỏ qua MAC address all-zeros
    valid_mac_addresses = []
    for mac in mac_addresses:
        if mac.upper() in ['00-00-00-00-00-00', '00:00:00:00:00:00']:
            print(f"Skipping invalid MAC: {mac}")
            continue
        valid_mac_addresses.append(mac)

    if not valid_mac_addresses:
        print("ERROR: No valid MAC addresses found (all are 00:00:00:00:00:00)")
        return False

    try:
        with open(MAC_TEMPLATE_FILE, 'w') as f:
            for mac in valid_mac_addresses:
                f.write(mac + '\n')

        print(f"Saved {len(valid_mac_addresses)} MAC address(es) to: {MAC_TEMPLATE_FILE}")
        for mac in valid_mac_addresses:
            print(f"  - {mac}")
        return True

    except Exception as e:
        print(f"ERROR: Could not save MAC addresses: {e}")
        return False


def is_template_vm():
    """Check if current machine is a template VM (MAC matches template file)"""
    if not os.path.exists(MAC_TEMPLATE_FILE):
        return False

    try:
        # Read template MAC addresses
        with open(MAC_TEMPLATE_FILE, 'r') as f:
            template_macs = [line.strip().upper() for line in f.readlines() if line.strip()]

        if not template_macs:
            return False

        # Get current MAC addresses
        current_macs = get_all_mac_addresses()

        # Check if any current MAC matches template MAC
        for current_mac in current_macs:
            if current_mac.upper() in template_macs:
                logging.warning(f"MAC {current_mac} matches template - this is a template VM")
                return True

        return False

    except Exception as e:
        logging.warning(f"Error checking template VM: {e}")
        return False


def get_mac_address():
    """Get MAC address of first active network interface (cross-platform)"""
    logging.info("Getting network adapter information...")

    try:
        if SYSTEM == "Windows":
            result = subprocess.run(
                ["ipconfig", "/all"],
                capture_output=True,
                text=True,
                timeout=5
            )

            for line in result.stdout.split('\n'):
                if 'Physical Address' in line:
                    match = re.search(r':\s*([0-9A-Fa-f\-]{17})', line)
                    if match:
                        mac = match.group(1).strip()
                        # Bỏ qua MAC address all-zeros
                        if mac.upper() in ['00-00-00-00-00-00', '00:00:00:00:00:00']:
                            logging.info(f"Skipping invalid MAC: {mac}")
                            continue
                        logging.info(f"MAC Address: {mac}")
                        return mac
        else:
            sys_net = '/sys/class/net'
            logging.debug(f"Reading MAC from {sys_net}")
            for iface in sorted(os.listdir(sys_net)):
                if iface == 'lo':
                    continue
                mac_file = os.path.join(sys_net, iface, 'address')
                if os.path.exists(mac_file):
                    with open(mac_file) as f:
                        mac = f.read().strip()
                    logging.debug(f"Interface {iface}: {mac}")
                    if mac and mac.upper() not in ['00:00:00:00:00:00', '00-00-00-00-00-00']:
                        logging.info(f"MAC Address: {mac}")
                        return mac

        logging.error("Could not get MAC address")
        return None

    except Exception as e:
        logging.error(f"Error getting MAC address: {e}")
        return None


def query_metadata_server(mac_address):
    """Query metadata server for IP configuration"""
    logging.info("Querying metadata server...")

    for attempt in range(1, MAX_RETRIES + 1):
        try:
            logging.info(f"Attempt {attempt}/{MAX_RETRIES}...")

            url = f"{METADATA_URL}?mac={mac_address}"
            logging.info(f"URL: {url}")

            req = Request(url, headers={'User-Agent': 'init-network-py/1.0'})
            with urlopen(req, timeout=10) as response:
                metadata_response = response.read().decode('utf-8').strip()

            if metadata_response:
                logging.info(f"Metadata received: {metadata_response}")
                return metadata_response
            else:
                logging.warning("Empty response from server")

        except URLError as e:
            logging.warning(f"Connection error: {e}")
        except Exception as e:
            logging.warning(f"Error querying metadata server: {e}")

        if attempt < MAX_RETRIES:
            logging.info(f"Retrying in {RETRY_INTERVAL} seconds...")
            time.sleep(RETRY_INTERVAL)

    logging.error(f"Failed to get metadata after {MAX_RETRIES} attempts")
    return None


def parse_metadata(metadata_str):
    """Parse comma-separated metadata response
    Format: ip,subnet_mask,gateway,dns1,dns2,hostname,user=username,uuid=uuid_value
    """
    try:
        parts = [p.strip() for p in metadata_str.split(',')]

        if len(parts) < 6:
            logging.error("Invalid metadata format")
            return None

        config = {
            'ip_address': parts[0],
            'subnet_mask': parts[1],
            'gateway': parts[2],
            'dns1': parts[3],
            'dns2': parts[4],
            'hostname': parts[5].split('=')[0] if '=' not in parts[5] or parts[5].startswith('user=') else parts[5] if parts[5] != 'null' else None,
            'user': None,
            'uuid': None
        }

        # Parse optional user and uuid parameters
        for i in range(6, len(parts)):
            part = parts[i]
            if part.startswith('user='):
                config['user'] = part.split('=', 1)[1]
            elif part.startswith('uuid='):
                config['uuid'] = part.split('=', 1)[1]

        logging.info("Configuration:")
        logging.info(f"   IP: {config['ip_address']}")
        logging.info(f"   Subnet: {config['subnet_mask']}")
        logging.info(f"   Gateway: {config['gateway']}")
        logging.info(f"   DNS1: {config['dns1']}")
        logging.info(f"   DNS2: {config['dns2']}")
        logging.info(f"   Hostname: {config['hostname']}")
        if config['user']:
            logging.info(f"   User: {config['user']}")
        if config['uuid']:
            logging.info(f"   UUID: {config['uuid']}")

        return config

    except Exception as e:
        logging.error(f"Error parsing metadata: {e}")
        return None


def mask_to_cidr(mask):
    """Convert subnet mask to CIDR notation (255.255.255.0 -> 24)"""
    try:
        parts = mask.split('.')
        binary = ''.join([bin(int(p) + 256)[3:] for p in parts])
        return str(binary.count('1'))
    except:
        return "24"


def generate_password_from_uuid(uuid_str):
    """Generate password from UUID using last 8 characters of MD5 hash"""
    try:
        md5_hash = hashlib.md5(uuid_str.encode()).hexdigest()
        password = "P" + md5_hash[-8:] + "@12"
        logging.info(f"Generated password from UUID (last 8 chars of MD5)")
        return password
    except Exception as e:
        logging.error(f"Error generating password from UUID: {e}")
        return None


def is_password_already_set():
    """Check if password was already set by checking flag file"""
    return os.path.exists(PASSWORD_SET_FLAG)


def mark_password_set():
    """Create flag file to indicate password was set successfully"""
    try:
        with open(PASSWORD_SET_FLAG, 'w') as f:
            f.write(f"{time.strftime('%Y-%m-%d %H:%M:%S')}\n")
        logging.info(f"Created password flag file: {PASSWORD_SET_FLAG}")
        return True
    except Exception as e:
        logging.error(f"Error creating password flag file: {e}")
        return False


def check_is_non_local_ip(ip):
    """Check if IP is non-local (public)"""
    if ip.startswith('127.') or ip.startswith('255.'):
        return False

    if ip in ['0.0.0.0', '255.255.255.255']:
        return False

    try:
        octets = [int(x) for x in ip.split('.')]
    except:
        return False

    if octets[0] == 10:
        return False
    if octets[0] == 172 and 16 <= octets[1] <= 31:
        return False
    if octets[0] == 192 and octets[1] == 168:
        return False
    if octets[0] == 169 and octets[1] == 254:
        return False

    return True


def get_non_local_ip():
    """Check if system already has a non-local (non-private) IP address"""
    logging.info("Checking for existing non-local IPv4 addresses...")

    try:
        if SYSTEM == "Windows":
            result = subprocess.run(
                ["ipconfig", "/all"],
                capture_output=True,
                text=True,
                timeout=5
            )
            output = result.stdout

            for line in output.split('\n'):
                if 'IPv4 Address' in line and 'DHCP' not in line and 'Preferred' in line:
                    match = re.search(r':\s*(\d+\.\d+\.\d+\.\d+)', line)
                    if match:
                        ip = match.group(1)
                        if check_is_non_local_ip(ip):
                            logging.info(f"Found existing non-local IPv4: {ip}")
                            return ip
        else:
            result = subprocess.run(
                ["/usr/sbin/ip", "addr", "show"],
                capture_output=True,
                text=True,
                timeout=5
            )
            output = result.stdout

            for line in output.split('\n'):
                if ' inet ' in line and 'inet6' not in line:
                    match = re.search(r'inet\s+(\d+\.\d+\.\d+\.\d+)', line)
                    if match:
                        ip = match.group(1)
                        if check_is_non_local_ip(ip):
                            logging.info(f"Found existing non-local IPv4: {ip}")
                            return ip

        logging.info("No non-local IPv4 found")
        return None

    except Exception as e:
        logging.warning(f"Error checking for non-local IP: {e}")
        return None


def set_user_password_windows(username, password):
    """Set password for user on Windows"""
    logging.info(f"Setting password for user: {username}")

    try:
        # Use net user command to set password
        cmd = ["net", "user", username, password]
        result = subprocess.run(cmd, capture_output=True, text=True, timeout=10)

        if result.returncode == 0:
            logging.info(f"Password set successfully for user: {username}")
            mark_password_set()
            return True
        else:
            logging.error(f"Failed to set password: {result.stderr}")
            return False

    except Exception as e:
        logging.error(f"Error setting user password on Windows: {e}")
        return False


def apply_network_config_windows(config):
    """Apply network configuration on Windows using netsh"""
    logging.info("Configuring network interface (Windows)...")

    try:
        result = subprocess.run(
            ["netsh", "interface", "ipv4", "show", "interfaces"],
            capture_output=True,
            text=True,
            timeout=10
        )

        logging.debug(f"netsh output:\n{result.stdout}")

        interface_name = None
        for line in result.stdout.split('\n'):
            parts = line.split()
            if len(parts) >= 5 and parts[0].isdigit() and int(parts[0]) > 1:
                interface_name = ' '.join(parts[4:])
                logging.info(f"Found interface: {interface_name}")
                break

        if not interface_name:
            logging.error("Could not find network interface")
            return False

        logging.info(f"Interface Name: {interface_name}")

        # Check current IP configuration
        check_cmd = ["netsh", "interface", "ipv4", "show", "config", f'name="{interface_name}"']
        result = subprocess.run(check_cmd, capture_output=True, text=True, timeout=10)
        config_output = result.stdout

        is_dhcp = "DHCP enabled" in config_output
        current_ip = None

        for line in config_output.split('\n'):
            if "IP Address" in line and ":" in line:
                parts = line.split(":")
                if len(parts) > 1:
                    current_ip = parts[1].strip()
                    break

        logging.info(f"Current config - DHCP: {is_dhcp}, IP: {current_ip}")

        is_lan_ip = current_ip and check_is_non_local_ip(current_ip) == False

        if current_ip and not is_lan_ip:
            logging.warning(f"[SKIP] Interface already has public IP ({current_ip})")
            return True

        # Reset to DHCP first
        logging.info("Resetting interface to DHCP...")
        dhcp_cmd = ["netsh", "interface", "ipv4", "set", "address",
                    f'name="{interface_name}"', "dhcp"]
        subprocess.run(dhcp_cmd, capture_output=True, text=True, timeout=10)

        time.sleep(2)

        # Set static IP
        logging.info(f"Setting IP: {config['ip_address']} / {config['subnet_mask']} / {config['gateway']}")

        cmd = [
            "netsh", "interface", "ipv4", "set", "address",
            f'name="{interface_name}"',
            "static",
            config['ip_address'],
            config['subnet_mask'],
            config['gateway']
        ]

        result = subprocess.run(cmd, capture_output=True, text=True, timeout=10)

        if result.returncode != 0:
            logging.error(f"[FAILED] netsh set address error code {result.returncode}")
            logging.error(f"  stderr: {result.stderr}")
        else:
            logging.info("[OK] IP address set successfully")

        # Set Primary DNS
        if config.get('dns1'):
            logging.info(f"Setting DNS1: {config['dns1']}")
            cmd = ["netsh", "interface", "ipv4", "set", "dnsservers",
                   f"name={interface_name}", "static", config['dns1']]
            subprocess.run(cmd, capture_output=True, text=True, timeout=10)

        # Set Secondary DNS
        if config.get('dns2'):
            logging.info(f"Setting DNS2: {config['dns2']}")
            cmd = ["netsh", "interface", "ipv4", "add", "dnsservers",
                   f"name={interface_name}", config['dns2']]
            subprocess.run(cmd, capture_output=True, text=True, timeout=10)

        # Set hostname
        if config.get('hostname'):
            logging.info(f"Setting computer name: {config['hostname']}")
            try:
                subprocess.run(
                    ["wmic", "computersystem", "where", f"name='{socket.gethostname()}'",
                     "rename", f"name='{config['hostname']}'"],
                    capture_output=True,
                    timeout=10
                )
            except Exception as e:
                logging.warning(f"Could not set hostname: {e}")

        return True

    except Exception as e:
        logging.error(f"Error applying network config on Windows: {e}")
        return False


def set_user_password_linux(username, password):
    """Set password for user on Linux"""
    logging.info(f"Setting password for user: {username}")

    try:
        # Use chpasswd command
        process = subprocess.Popen(
            ['chpasswd'],
            stdin=subprocess.PIPE,
            stdout=subprocess.PIPE,
            stderr=subprocess.PIPE,
            text=True
        )

        stdout, stderr = process.communicate(input=f"{username}:{password}\n", timeout=10)

        if process.returncode == 0:
            logging.info(f"Password set successfully for user: {username}")
            mark_password_set()
            return True
        else:
            logging.error(f"Failed to set password: {stderr}")
            return False

    except Exception as e:
        logging.error(f"Error setting user password on Linux: {e}")
        return False


def get_first_ethernet_interface():
    """Get first found ethernet interface name on Linux (not loopback)"""
    SKIP_IFACES = {'lo', 'docker0', 'virbr0', 'virbr0-nic', 'br-'}
    try:
        # Try 'ip link show' to list all interfaces
        result = subprocess.run(
            ['/usr/sbin/ip', 'link', 'show'],
            capture_output=True,
            text=True,
            timeout=5
        )

        for line in result.stdout.split('\n'):
            # Only match interface index lines: "2: ens192: <FLAGS> ..."
            # NOT sub-lines like "    link/ether 00:50:56:b7:fd:e9 brd ff:ff:ff:ff:ff:ff"
            match = re.match(r'^\d+:\s+(\S+):', line)
            if match:
                iface_name = match.group(1).split('@')[0]  # Handle veth@eth0 format
                if iface_name and iface_name not in SKIP_IFACES and not iface_name.startswith('br-'):
                    logging.info(f"Found ethernet interface: {iface_name}")
                    return iface_name

        # Fallback: read from /sys/class/net
        sys_net = '/sys/class/net'
        if os.path.exists(sys_net):
            for iface_name in sorted(os.listdir(sys_net)):
                if iface_name not in SKIP_IFACES and not iface_name.startswith('br-'):
                    logging.info(f"Found ethernet interface (sysfs): {iface_name}")
                    return iface_name

    except Exception as e:
        logging.warning(f"Error detecting interface: {e}")

    # Default fallback
    logging.warning("Could not detect interface name, using eth0 as default")
    return 'eth0'


def reset_interface_to_dhcp_linux():
    """Reset first found ethernet interface to DHCP on Linux"""
    logging.info("Attempting to reset interface to DHCP first...")
    
    try:
        # Check if NetworkManager is running
        try:
            result = subprocess.run(
                ['systemctl', 'is-active', 'NetworkManager'],
                capture_output=True,
                text=True,
                timeout=5
            )
            if result.returncode == 0 and result.stdout.strip() == 'active':
                logging.info("Using NetworkManager to reset to DHCP")
                # Find first ethernet connection
                result = subprocess.run(
                    ['nmcli', '-t', '-f', 'NAME,TYPE', 'connection', 'show'],
                    capture_output=True,
                    text=True,
                    timeout=5
                )
                for line in result.stdout.strip().split('\n'):
                    if line:
                        parts = line.split(':')
                        if len(parts) >= 2 and 'ethernet' in parts[1].lower():
                            connection_name = parts[0]
                            logging.info(f"Resetting {connection_name} to DHCP via nmcli")
                            cmd = ['nmcli', 'connection', 'modify', connection_name, 'ipv4.method', 'auto']
                            subprocess.run(cmd, capture_output=True, text=True, timeout=10)
                            subprocess.run(['nmcli', 'connection', 'down', connection_name],
                                         capture_output=True, timeout=10)
                            time.sleep(2)
                            subprocess.run(['nmcli', 'connection', 'up', connection_name],
                                         capture_output=True, timeout=10)
                            logging.info("Interface reset to DHCP")
                            return True
        except:
            pass
        
        # Fallback to netplan
        if os.path.exists('/etc/netplan'):
            logging.info("Using netplan to reset to DHCP")
            iface = get_first_ethernet_interface()
            netplan_config = f"""network:
  version: 2
  ethernets:
    {iface}:
      dhcp4: yes
"""
            config_file = "/etc/netplan/01-dhcp-reset.yaml"
            with open(config_file, 'w') as f:
                f.write(netplan_config)
            subprocess.run(["netplan", "apply"], capture_output=True, timeout=30)
            logging.info("Interface reset to DHCP via netplan")
            time.sleep(3)
            return True
        
        # Fallback to ifupdown
        logging.info("Using ifupdown to reset to DHCP")
        iface = get_first_ethernet_interface()
        interfaces_config = f"""auto {iface}
iface {iface} inet dhcp
"""
        config_file = f"/etc/network/interfaces.d/{iface}-dhcp"
        with open(config_file, 'w') as f:
            f.write(interfaces_config)
        subprocess.run(["systemctl", "restart", "networking"], capture_output=True, timeout=30)
        logging.info("Interface reset to DHCP via ifupdown")
        return True
        
    except Exception as e:
        logging.warning(f"Could not reset to DHCP: {e}")
        return False


def apply_network_config_linux(config):
    """Apply network configuration on Linux using NetworkManager, netplan or ifupdown"""
    logging.info("Configuring network interface (Linux)...")

    try:
        # Step 1: Reset to DHCP first
        reset_interface_to_dhcp_linux()
        time.sleep(2)
        
        # Step 2: Check if NetworkManager is running
        try:
            result = subprocess.run(
                ['systemctl', 'is-active', 'NetworkManager'],
                capture_output=True,
                text=True,
                timeout=5
            )
            nm_status = result.stdout.strip()
            if result.returncode == 0 and nm_status == 'active':
                logging.info("NetworkManager detected")
                nm_result = apply_networkmanager_config(config)
                if nm_result:
                    return True
                logging.warning("NetworkManager config failed, falling back to netplan/ifupdown")
        except:
            pass

        # Fallback to netplan or ifupdown
        netplan_available = os.path.exists('/etc/netplan')

        if netplan_available:
            return apply_netplan_config(config)
        else:
            return apply_ifupdown_config(config)

    except Exception as e:
        logging.error(f"Error applying network config on Linux: {e}")
        return False


def apply_networkmanager_config(config):
    """Apply network config using NetworkManager (nmcli)"""
    logging.info("Using NetworkManager for network configuration...")

    try:
        # Get first active ethernet connection
        result = subprocess.run(
            ['nmcli', '-t', '-f', 'NAME,TYPE,DEVICE', 'connection', 'show', '--active'],
            capture_output=True,
            text=True,
            timeout=5
        )

        connection_name = None
        for line in result.stdout.strip().split('\n'):
            if line:
                parts = line.split(':')
                if len(parts) >= 3 and 'ethernet' in parts[1].lower():
                    connection_name = parts[0]
                    logging.info(f"Found active ethernet connection: {connection_name}")
                    break

        if not connection_name:
            # Try to find any ethernet connection
            result = subprocess.run(
                ['nmcli', '-t', '-f', 'NAME,TYPE', 'connection', 'show'],
                capture_output=True,
                text=True,
                timeout=5
            )
            for line in result.stdout.strip().split('\n'):
                if line:
                    parts = line.split(':')
                    if len(parts) >= 2 and 'ethernet' in parts[1].lower():
                        connection_name = parts[0]
                        logging.info(f"Found ethernet connection: {connection_name}")
                        break

        if not connection_name:
            logging.error("Could not find ethernet connection")
            return False

        logging.info(f"Configuring connection: {connection_name}")

        # Configure static IP
        commands = [
            ['nmcli', 'connection', 'modify', connection_name, 'ipv4.method', 'manual'],
            ['nmcli', 'connection', 'modify', connection_name, 'ipv4.addresses',
             f"{config['ip_address']}/{mask_to_cidr(config['subnet_mask'])}"],
            ['nmcli', 'connection', 'modify', connection_name, 'ipv4.gateway', config['gateway']],
            ['nmcli', 'connection', 'modify', connection_name, 'ipv4.dns',
             f"{config['dns1']},{config['dns2']}"],
        ]

        for cmd in commands:
            logging.info(f"Running: {' '.join(cmd)}")
            result = subprocess.run(cmd, capture_output=True, text=True, timeout=10)
            if result.returncode != 0:
                logging.warning(f"Command failed: {result.stderr}")

        # Restart connection
        logging.info("Restarting network connection...")
        subprocess.run(['nmcli', 'connection', 'down', connection_name],
                      capture_output=True, timeout=10)
        time.sleep(2)
        subprocess.run(['nmcli', 'connection', 'up', connection_name],
                      capture_output=True, timeout=30)

        logging.info("NetworkManager configuration applied")

        # Set hostname
        if config.get('hostname'):
            logging.info(f"Setting hostname: {config['hostname']}")
            subprocess.run(
                ['hostnamectl', 'set-hostname', config['hostname']],
                check=True,
                timeout=10
            )

        return True

    except Exception as e:
        logging.error(f"Error applying NetworkManager config: {e}")
        return False


def apply_netplan_config(config):
    """Apply network config using netplan"""
    logging.info("Using netplan for network configuration...")

    try:
        iface = get_first_ethernet_interface()
        logging.info(f"Using interface: {iface}")
        
        netplan_config = f"""
network:
  version: 2
  ethernets:
    {iface}:
      dhcp4: no
      addresses:
        - {config['ip_address']}/{mask_to_cidr(config['subnet_mask'])}
      gateway4: {config['gateway']}
      nameservers:
        addresses:
          - {config['dns1']}
          - {config['dns2']}
"""

        config_file = "/etc/netplan/01-init-network.yaml"

        with open(config_file, 'w') as f:
            f.write(netplan_config)

        logging.info(f"Wrote netplan config to {config_file}")

        subprocess.run(["netplan", "apply"], check=True, timeout=30)
        logging.info("Netplan configuration applied")

        # Wait for interface to come up after netplan apply
        logging.info("Waiting for interface to apply configuration...")
        time.sleep(5)

        if config.get('hostname'):
            logging.info(f"Setting hostname: {config['hostname']}")
            subprocess.run(
                ["hostnamectl", "set-hostname", config['hostname']],
                check=True,
                timeout=10
            )

        return True

    except Exception as e:
        logging.error(f"Error applying netplan config: {e}")
        return False


def apply_ifupdown_config(config):
    """Apply network config using ifupdown (Debian/Ubuntu classic)"""
    logging.info("Using ifupdown for network configuration...")

    try:
        iface = get_first_ethernet_interface()
        logging.info(f"Using interface: {iface}")
        
        interfaces_config = f"""
auto {iface}
iface {iface} inet static
    address {config['ip_address']}
    netmask {config['subnet_mask']}
    gateway {config['gateway']}
    dns-nameservers {config['dns1']} {config['dns2']}
"""

        config_file = f"/etc/network/interfaces.d/{iface}"

        with open(config_file, 'w') as f:
            f.write(interfaces_config)

        logging.info(f"Wrote interfaces config to {config_file}")

        subprocess.run(["systemctl", "restart", "networking"], check=True, timeout=30)
        logging.info("Network service restarted")

        if config.get('hostname'):
            logging.info(f"Setting hostname: {config['hostname']}")
            subprocess.run(
                ["hostnamectl", "set-hostname", config['hostname']],
                check=True,
                timeout=10
            )

        return True

    except Exception as e:
        logging.error(f"Error applying ifupdown config: {e}")
        return False


def verify_ip_config(ip_address):
    """Verify that IP was set correctly"""
    logging.info("Verifying IP configuration...")

    try:
        if SYSTEM == "Windows":
            result = subprocess.run(
                ["ipconfig", "/all"],
                capture_output=True,
                text=True,
                timeout=5
            )
        else:
            result = subprocess.run(
                ["/usr/sbin/ip", "addr", "show"],
                capture_output=True,
                text=True,
                timeout=5
            )

        if ip_address in result.stdout:
            logging.info(f"IP address verified: {ip_address}")
            return True
        else:
            logging.warning(f"IP address {ip_address} not found in network config")
            return False

    except Exception as e:
        logging.warning(f"Error verifying IP: {e}")
        return False


def test_internet_connectivity():
    """Test internet connectivity by pinging 8.8.8.8"""
    logging.info("Testing internet connectivity...")

    try:
        if SYSTEM == "Windows":
            result = subprocess.run(
                ["ping", "-n", "4", "-w", "5000", "8.8.8.8"],
                capture_output=True,
                timeout=30
            )
        else:
            result = subprocess.run(
                ["ping", "-c", "4", "-W", "5", "8.8.8.8"],
                capture_output=True,
                timeout=30
            )

        if result.returncode == 0:
            logging.info("Internet connectivity verified (8.8.8.8 ping successful)")
            return True
        else:
            logging.warning("Cannot ping 8.8.8.8, but continuing...")
            return False

    except Exception as e:
        logging.warning(f"Ping error: {e}")
        return False


def callback_metadata_server(mac_address):
    """Callback to metadata server with set_ip_done=1"""
    logging.info("Notifying metadata server (set_ip_done=1)...")

    try:
        url = f"{METADATA_URL2}?mac={mac_address}&set_ip_done=1"
        logging.info(f"Callback URL: {url}")

        # Bỏ qua SSL verification
        ssl_context = ssl.create_default_context()
        ssl_context.check_hostname = False
        ssl_context.verify_mode = ssl.CERT_NONE

        req = Request(url, headers={'User-Agent': 'init-network-py/1.0'})
        with urlopen(req, timeout=10, context=ssl_context) as response:
            callback_response = response.read().decode('utf-8').strip()

        if callback_response:
            logging.info(f"Callback response: {callback_response}")
        else:
            logging.info("No response from callback (may be normal)")

        return True

    except Exception as e:
        logging.warning(f"Callback error: {e}")
        return False


def perform_network_config():
    """Perform full network configuration"""
    logging.info("=" * 70)
    logging.info("Network Configuration Started")
    logging.info("=" * 70)
    logging.info(f"System: {SYSTEM}")

    # Step 0: Check if this is a template VM
    logging.info("Step 0: Checking if this is a template VM...")
    if is_template_vm():
        logging.warning("=" * 70)
        logging.warning("IGNORE because MAC address is template VM")
        logging.warning("=" * 70)
        return True

    # Step 1: Check if already have internet connectivity
    logging.info("Step 1: Checking existing internet connectivity...")

    if test_internet_connectivity():
        logging.info("Internet is already accessible! Skipping network configuration.")
        return True

    # Check if already has a non-local IP
    logging.info("Checking for existing non-local IP address...")
    existing_ip = get_non_local_ip()

    if existing_ip:
        logging.info(f"Non-local IP already configured: {existing_ip}")
        return True

    logging.info("No non-local IP detected, proceeding with network configuration...")

    # Step 2: Get MAC address (retry up to 60s for interfaces to come up)
    logging.info("Step 2: Getting network adapter information...")
    mac_address = None
    for attempt in range(12):
        mac_address = get_mac_address()
        if mac_address:
            break
        logging.warning(f"MAC not found (attempt {attempt + 1}/12), retrying in 5s...")
        time.sleep(5)
    if not mac_address:
        logging.error("Failed to get MAC address after 60s")
        return False

    # Step 3: Query metadata server
    logging.info("Step 3: Querying metadata server...")
    metadata_response = query_metadata_server(mac_address)
    if not metadata_response:
        logging.error("Failed to get metadata from server")
        return False

    # Step 4: Parse configuration
    logging.info("Step 4: Parsing configuration...")
    config = parse_metadata(metadata_response)
    if not config:
        logging.error("Failed to parse metadata")
        return False

    # Step 5: Apply network configuration
    logging.info("Step 5: Applying network configuration...")
    if SYSTEM == "Windows":
        success = apply_network_config_windows(config)
    else:
        success = apply_network_config_linux(config)

    if not success:
        logging.warning("Network configuration may have failed")
        logging.error("Skipping password setup due to network configuration failure")
        return False

    # Step 6: Verify IP configuration
    logging.info("Step 6: Verifying IP configuration...")
    time.sleep(3)
    ip_verified = verify_ip_config(config['ip_address'])

    # Step 7: Test internet connectivity
    logging.info("Step 7: Testing internet connectivity...")
    test_internet_connectivity()

    # Step 8: Set user password if provided (only if network config was successful)
    if ip_verified and config.get('user') and config.get('uuid'):
        if is_password_already_set():
            logging.info("Step 8: Password already set previously, skipping...")
        else:
            logging.info("Step 8: Setting user password...")
            password = generate_password_from_uuid(config['uuid'])
            if password:
                if SYSTEM == "Windows":
                    set_user_password_windows(config['user'], password)
                else:
                    set_user_password_linux(config['user'], password)
            else:
                logging.warning("Could not generate password from UUID")
    elif not ip_verified:
        logging.warning("Step 8: Skipping password setup - IP verification failed")
    else:
        logging.info("Step 8: No user/uuid provided, skipping password setup")

    # Step 9: Callback to metadata server
    logging.info("Step 9: Notifying metadata server...")
    callback_metadata_server(mac_address)

    logging.info("=" * 70)
    logging.info("Network Configuration Completed")
    logging.info("=" * 70)

    return True


# ============================================================================
# Ping Function
# ============================================================================

def ping_host(host='8.8.8.8'):
    """Ping một host và trả về kết quả"""
    try:
        if SYSTEM == "Windows":
            cmd = ['ping', '-n', '1', host]
        else:
            cmd = ['ping', '-c', '1', host]
        
        result = subprocess.run(
            cmd,
            capture_output=True,
            text=True,
            timeout=10
        )

        if result.returncode == 0:
            logging.info(f"Ping {host}: SUCCESS")
            return True
        else:
            logging.info(f"Ping {host}: FAILED")
            return False

    except subprocess.TimeoutExpired:
        logging.error(f"Ping {host}: TIMEOUT")
        return False
    except Exception as e:
        logging.error(f"Ping {host}: ERROR - {str(e)}")
        return False


# ============================================================================
# Windows Service Class (Windows only)
# ============================================================================

if SYSTEM == "Windows":
    class PingService(win32serviceutil.ServiceFramework):
        """Windows Service để ping 8.8.8.8"""

        _svc_name_ = SERVICE_NAME
        _svc_display_name_ = _svc_name_
        _svc_description_ = f"Service Set IP Glx VPS"
        _svc_start_type_ = win32service.SERVICE_AUTO_START  # Auto start khi Windows boot

        def __init__(self, args):
            win32serviceutil.ServiceFramework.__init__(self, args)
            self.hWaitStop = win32event.CreateEvent(None, 0, 0, None)
            self.is_running = True

        def SvcStop(self):
            """Dừng service"""
            self.ReportServiceStatus(win32service.SERVICE_STOP_PENDING)
            self.is_running = False
            win32event.SetEvent(self.hWaitStop)
            logging.info("Service dừng lại")

        def SvcDoRun(self):
            """Chạy service"""
            servicemanager.LogMsg(
                servicemanager.EVENTLOG_INFORMATION_TYPE,
                servicemanager.PYS_SERVICE_STARTED,
                (self._svc_name_, '')
            )

            setup_logging()
            logging.info("=" * 50)
            logging.info("Service ping 8.8.8.8 bắt đầu")
            logging.info(f"Log file: {LOG_FILE}")
            logging.info("=" * 50)

            # Thực hiện cấu hình mạng khi service khởi động
            logging.info("Running initial network configuration...")
            perform_network_config()

            # Main loop - ping mỗi 60 giây
            while self.is_running:
                ping_host('8.8.8.8')
                # Chờ 60 giây hoặc cho đến khi nhận tín hiệu stop
                if win32event.WaitForSingleObject(self.hWaitStop, 60000) == win32event.WAIT_OBJECT_0:
                    break


# ============================================================================
# Linux Systemd Service Functions
# ============================================================================

def create_systemd_service():
    """Create systemd service file for Linux"""
    service_content = f"""[Unit]
Description=Glx Network Configuration Service
After=network-online.target
Wants=network-online.target

[Service]
Type=simple
ExecStart=/usr/bin/python3 {os.path.abspath(__file__)} --daemon
Restart=always
RestartSec=60

[Install]
WantedBy=multi-user.target
"""

    service_file = "/etc/systemd/system/glx-service.service"

    try:
        with open(service_file, 'w') as f:
            f.write(service_content)

        print(f"Created systemd service: {service_file}")

        # Reload systemd
        subprocess.run(['systemctl', 'daemon-reload'], check=True)
        print("Systemd daemon reloaded")

        # Enable service
        subprocess.run(['systemctl', 'enable', 'glx-service'], check=True)
        print("Service enabled to start on boot")

        return True

    except PermissionError:
        print("ERROR: Permission denied. Run with sudo.")
        return False
    except Exception as e:
        print(f"ERROR: {e}")
        return False


def remove_systemd_service():
    """Remove systemd service"""
    try:
        subprocess.run(['systemctl', 'stop', 'glx-service'], capture_output=True)
        subprocess.run(['systemctl', 'disable', 'glx-service'], capture_output=True)

        service_file = "/etc/systemd/system/glx-service.service"
        if os.path.exists(service_file):
            os.remove(service_file)

        subprocess.run(['systemctl', 'daemon-reload'], check=True)
        print("Service removed successfully")
        return True

    except Exception as e:
        print(f"ERROR: {e}")
        return False


def run_linux_daemon(console=False):
    """Run as Linux daemon (console=True for foreground/standalone mode)"""
    setup_logging(console=console)
    logging.info("=" * 50)
    logging.info("Glx Service started (Linux)")
    logging.info(f"Log file: {LOG_FILE}")
    logging.info("=" * 50)

    # Thực hiện cấu hình mạng
    logging.info("Running initial network configuration...")
    perform_network_config()

    # Main loop - ping mỗi 60 giây
    try:
        while True:
            ping_host('8.8.8.8')
            time.sleep(60)
    except KeyboardInterrupt:
        logging.info("Service stopped by user")
    except Exception as e:
        logging.error(f"Service error: {e}")


# ============================================================================
# Main Entry Point
# ============================================================================

if __name__ == '__main__':
    # Check for 'mac' argument - save template MAC addresses
    if len(sys.argv) > 1 and 'mac' in sys.argv[1].lower():
        save_template_mac_addresses()
        sys.exit(0)

    if SYSTEM == "Windows":
        # Windows Service
        if len(sys.argv) == 1:
            # Chạy như service
            servicemanager.Initialize()
            servicemanager.PrepareToHostSingle(PingService)
            servicemanager.StartServiceCtrlDispatcher()
        else:
            # Xử lý command line (install, start, stop, remove)
            win32serviceutil.HandleCommandLine(PingService)

            # Sau khi install, set startup type thành Auto
            if len(sys.argv) > 1 and sys.argv[1].lower() == 'install':
                try:
                    subprocess.run(
                        ['sc', 'config', SERVICE_NAME, 'start=', 'auto'],
                        capture_output=True,
                        timeout=10
                    )
                    print(f"Service '{SERVICE_NAME}' set to Auto start")
                except Exception as e:
                    print(f"Warning: Could not set auto start: {e}")

    else:
        # Linux - systemd service management
        if len(sys.argv) > 1:
            command = sys.argv[1].lower()

            if command == 'install':
                print("Installing systemd service...")
                if create_systemd_service():
                    print("Service installed successfully")
                    print("Start service: sudo systemctl start glx-service")
                else:
                    sys.exit(1)

            elif command == 'remove' or command == 'uninstall':
                print("Removing systemd service...")
                remove_systemd_service()

            elif command == 'start':
                subprocess.run(['systemctl', 'start', 'glx-service'])
                print("Service started")

            elif command == 'stop':
                subprocess.run(['systemctl', 'stop', 'glx-service'])
                print("Service stopped")

            elif command == 'status':
                subprocess.run(['systemctl', 'status', 'glx-service'])

            elif command == '--daemon':
                # Run as daemon (called by systemd)
                run_linux_daemon(console=False)

            else:
                print("Usage: python3 setip.py [install|remove|start|stop|status|mac]")
                sys.exit(1)
        else:
            # Run directly (foreground mode for testing)
            print("Running in foreground mode (Ctrl+C to stop)")
            print("For production, use: sudo python3 setip.py install")
            run_linux_daemon(console=True)
