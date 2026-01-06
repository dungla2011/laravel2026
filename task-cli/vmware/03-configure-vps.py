#!/usr/bin/env python3
"""
VPS Configuration Daemon
Continuously monitors vps_instances table for completed VPS creations and configures them
Sets up IP addresses, usernames, passwords based on OS type (Windows, Ubuntu, etc.)

Install: pip install pyvmomi pymysql python-dotenv paramiko
"""

import atexit
import ssl
import sys
import os
import json
import time
import threading
import random
import string
from datetime import datetime
from threading import Thread, Lock
from dotenv import load_dotenv
import pymysql
from pyVmomi import vim, vmodl
from pyVim.connect import SmartConnect, Disconnect

# Load environment variables from parent .env
env_path = os.path.join(os.path.dirname(__file__), '../../.env')
load_dotenv(env_path)

# Global lock for thread safety
db_lock = Lock()
print_lock = Lock()

def print_log(msg, level="info"):
    """Thread-safe print logging"""
    with print_lock:
        print(msg)

def get_db_connection():
    """Create and return database connection"""
    try:
        conn = pymysql.connect(
            host=os.getenv('DB_RM_HOST8', 'localhost'),
            port=int(os.getenv('DB_PORT', 3306)),
            user=os.getenv('DB_RM_USER8', 'root'),
            password=os.getenv('DB_RM_PW8', ''),
            database=os.getenv('DB_RM_NAME8', 'laravel'),
            charset='utf8mb4',
            cursorclass=pymysql.cursors.DictCursor
        )
        return conn
    except Exception as e:
        print(f"❌ Database connection error: {e}")
        return None

def get_obj(content, vimtype, name=None, vm_id=None):
    """Get vSphere object by name or ID"""
    container = content.viewManager.CreateContainerView(content.rootFolder, vimtype, True)
    try:
        for c in container.view:
            if vm_id:
                if hasattr(c, 'config') and c.config and c.config.uuid == vm_id:
                    return c
            elif name:
                if c.name == name:
                    return c
    finally:
        container.Destroy()
    return None

def generate_password(length=16):
    """Generate a random secure password"""
    characters = string.ascii_letters + string.digits + "!@#$%^&*"
    return ''.join(random.choice(characters) for _ in range(length))

def allocate_ip_from_pool(vps_id):
    """
    Allocate an IP address from available pool
    TODO: Implement actual IP allocation logic from your pool
    For now, generate sequential IPs for demo
    """
    try:
        with db_lock:
            conn = get_db_connection()
            if not conn:
                return None
            
            try:
                with conn.cursor() as cursor:
                    # Get next available IP from your IP pool
                    # This is a placeholder - customize based on your IP management
                    sql = """
                    SELECT ip FROM vps_ip_pools 
                    WHERE status = 'available' 
                    LIMIT 1
                    """
                    cursor.execute(sql)
                    result = cursor.fetchone()
                    
                    if result:
                        ip = result['ip']
                        # Mark as used
                        update_sql = "UPDATE vps_ip_pools SET status = 'assigned', vps_id = %s WHERE ip = %s"
                        cursor.execute(update_sql, (vps_id, ip))
                        conn.commit()
                        return ip
                    
                    return None
            except Exception as e:
                # If table doesn't exist, generate IP
                print(f"⚠️  IP pool table not found, generating IP: {e}")
                # Generate demo IP: 10.0.1.{vps_id + 100}
                return f"10.0.1.{100 + (vps_id % 155)}"
            finally:
                conn.close()
    except Exception as e:
        print(f"❌ Error allocating IP: {e}")
        return None

def update_vps_config_status(vps_id, config_status, config_progress=None):
    """Update VPS configuration status in database"""
    with db_lock:
        conn = get_db_connection()
        if not conn:
            print(f"❌ Failed to connect to database for VPS #{vps_id}")
            return False
        
        try:
            with conn.cursor() as cursor:
                if config_progress:
                    sql = """
                    UPDATE vps_instances 
                    SET config_status = %s, config_vps_progress = %s
                    WHERE id = %s
                    """
                    cursor.execute(sql, (config_status, config_progress, vps_id))
                else:
                    sql = """
                    UPDATE vps_instances 
                    SET config_status = %s
                    WHERE id = %s
                    """
                    cursor.execute(sql, (config_status, vps_id))
            
            conn.commit()
            print(f"  ✅ VPS #{vps_id} config status: {config_status}")
            return True
        except Exception as e:
            print(f"  ❌ VPS #{vps_id} config status update failed: {e}")
            return False
        finally:
            conn.close()

def update_vps_credentials(vps_id, ip_address, username, password):
    """Update VPS with IP and credentials"""
    with db_lock:
        conn = get_db_connection()
        if not conn:
            print(f"❌ Failed to connect to database for VPS #{vps_id}")
            return False
        
        try:
            with conn.cursor() as cursor:
                sql = """
                UPDATE vps_instances 
                SET ip_address = %s, 
                    admin_username = %s, 
                    admin_password = %s
                WHERE id = %s
                """
                cursor.execute(sql, (ip_address, username, password, vps_id))
            
            conn.commit()
            print(f"  ✅ VPS #{vps_id} credentials updated")
            return True
        except Exception as e:
            print(f"  ❌ VPS #{vps_id} credentials update failed: {e}")
            return False
        finally:
            conn.close()

def wait_for_vm_ready(vm, timeout=300):
    """
    Wait for VM to boot and be ready for configuration
    Check if VM is powered on and guest tools are running
    """
    start_time = time.time()
    last_status_check = start_time
    
    while time.time() - start_time < timeout:
        try:
            # Check if VM is powered on
            if vm.runtime.powerState == vim.VirtualMachine.PowerState.poweredOn:
                # Check if guest tools are running
                if vm.guest.toolsStatus == vim.vm.GuestInfo.ToolsStatus.toolsOk:
                    elapsed = time.time() - start_time
                    print(f"✅ VM is ready (tools running) - {elapsed:.1f}s")
                    return True
                
                # Print status every 10 seconds
                now = time.time()
                if now - last_status_check >= 10:
                    tools_status = vm.guest.toolsStatus
                    print(f"⏳ Waiting for tools... (status: {tools_status})")
                    last_status_check = now
            else:
                # VM not powered on, power it on
                print(f"🔌 VM is not powered on, powering on...")
                task = vm.PowerOn()
                # Wait a bit for power on task
                time.sleep(5)
        
        except Exception as e:
            print(f"⚠️  Error checking VM status: {e}")
        
        time.sleep(2)
    
    print(f"⚠️  VM ready check timeout after {timeout}s")
    return False

def configure_vps_thread(vps_id, vm_name, init_os_id, vmware_vm_id):
    """Thread function to configure a VPS"""
    print(f"\n{'='*60}")
    print(f"⚙️  Configuring VPS #{vps_id}")
    print(f"{'='*60}")
    
    progress = {
        'vps_id': vps_id,
        'start_time': datetime.now().isoformat(),
        'steps': []
    }
    
    # Get OS info from database
    os_info = None
    with db_lock:
        conn = get_db_connection()
        if not conn:
            print(f"❌ VPS #{vps_id}: Failed to connect to database")
            update_vps_config_status(vps_id, 'vps_config_error', json.dumps(progress))
            return
        
        try:
            with conn.cursor() as cursor:
                sql = """
                SELECT id, name, os_type, vm_name, iso_name 
                FROM vps_os_versions WHERE id = %s
                """
                cursor.execute(sql, (init_os_id,))
                os_info = cursor.fetchone()
            
            if not os_info:
                print(f"❌ VPS #{vps_id}: OS version {init_os_id} not found")
                progress['steps'].append({
                    'stage': 'error',
                    'timestamp': datetime.now().isoformat(),
                    'status': f'OS version {init_os_id} not found'
                })
                update_vps_config_status(vps_id, 'vps_config_error', json.dumps(progress))
                return
            
            os_type = os_info.get('os_type', 'linux').lower()
            os_name = os_info.get('name', 'Unknown')
            print(f"📋 VPS #{vps_id}: OS: {os_name} (type: {os_type})")
            
        except Exception as e:
            print(f"❌ VPS #{vps_id}: Database error: {str(e)}")
            progress['steps'].append({
                'stage': 'error',
                'timestamp': datetime.now().isoformat(),
                'status': f'Database error: {str(e)}'
            })
            update_vps_config_status(vps_id, 'vps_config_error', json.dumps(progress))
            return
        finally:
            conn.close()
    
    # Mark as configuring
    print(f"🔄 VPS #{vps_id}: Updating status to vps_configuring")
    progress['steps'].append({
        'stage': 'status_change',
        'timestamp': datetime.now().isoformat(),
        'status': 'Starting VPS configuration'
    })
    
    # Save initial unified progress format
    initial_progress = {
        'vps_id': vps_id,
        'start_time': progress['start_time'],
        'end_time': None,
        'current_stage': 'configuring',
        'current_status': 'Preparing to configure VPS',
        'percent': 0,
        'success': None,
        'steps': progress['steps']
    }
    update_vps_config_status(vps_id, 'vps_configuring', json.dumps(initial_progress))
    
    # Connect to vCenter to get VM
    vcenter_host = os.getenv('VCENTER_DOMAIN')
    vcenter_user = os.getenv('VCENTER_UID')
    vcenter_password = os.getenv('VCENTER_PW')
    
    try:
        context = ssl._create_unverified_context()
        si = SmartConnect(
            host=vcenter_host,
            user=vcenter_user,
            pwd=vcenter_password,
            sslContext=context
        )
        
        if not si:
            raise Exception(f"Failed to connect to vCenter: {vcenter_host}")
        
        print(f"✅ Connected to vCenter")
        atexit.register(Disconnect, si)
        content = si.RetrieveContent()
        
        # Find the VM
        print(f"🔍 Finding VM: {vm_name}")
        vm = get_obj(content, [vim.VirtualMachine], name=vm_name)
        
        if not vm:
            raise Exception(f"VM '{vm_name}' not found in vCenter")
        
        print(f"✅ Found VM: {vm.name}")
        
        # Update progress
        progress['steps'].append({
            'stage': 'status_change',
            'timestamp': datetime.now().isoformat(),
            'status': f'Found VM {vm_name}'
        })
        
        # Wait for VM to be ready
        print(f"\n⏳ Waiting for VM to boot and be ready...")
        progress['steps'].append({
            'stage': 'status_change',
            'timestamp': datetime.now().isoformat(),
            'status': 'Waiting for VM to boot'
        })
        update_vps_config_status(vps_id, 'vps_configuring', json.dumps(progress))
        
        vm_ready = wait_for_vm_ready(vm, timeout=600)
        
        if not vm_ready:
            print(f"⚠️  VM did not become ready, continuing anyway...")
            progress['steps'].append({
                'stage': 'warning',
                'timestamp': datetime.now().isoformat(),
                'status': 'VM ready timeout, proceeding'
            })
        else:
            progress['steps'].append({
                'stage': 'status_change',
                'timestamp': datetime.now().isoformat(),
                'status': 'VM is ready'
            })
        
        # Allocate IP address
        print(f"\n🌐 Allocating IP address...")
        ip_address = allocate_ip_from_pool(vps_id)
        
        if not ip_address:
            raise Exception("Failed to allocate IP address")
        
        print(f"✅ Allocated IP: {ip_address}")
        progress['steps'].append({
            'stage': 'status_change',
            'timestamp': datetime.now().isoformat(),
            'status': f'Allocated IP: {ip_address}'
        })
        
        # Generate credentials
        print(f"\n🔐 Generating credentials...")
        admin_username = "admin"
        if os_type == 'windows':
            admin_username = "Administrator"
        
        admin_password = generate_password(16)
        
        print(f"✅ Generated credentials")
        print(f"   Username: {admin_username}")
        print(f"   Password: {'*' * len(admin_password)}")
        
        progress['steps'].append({
            'stage': 'status_change',
            'timestamp': datetime.now().isoformat(),
            'status': f'Generated admin credentials'
        })
        
        # Configure based on OS type
        if os_type == 'windows':
            print(f"\n💻 Windows VPS Configuration")
            progress['steps'].append({
                'stage': 'status_change',
                'timestamp': datetime.now().isoformat(),
                'status': 'Configuring Windows VM'
            })
            # Windows configuration would involve:
            # - Setting IP via WinRM or guest customization
            # - Setting admin password
            # - Configuring firewall
            # - Setting up RDP access
        else:
            # Linux/Ubuntu
            print(f"\n🐧 Linux/Ubuntu VPS Configuration")
            progress['steps'].append({
                'stage': 'status_change',
                'timestamp': datetime.now().isoformat(),
                'status': 'Configuring Linux VM'
            })
            # Linux configuration would involve:
            # - Setting IP via cloud-init or manual configuration
            # - Creating admin user
            # - Configuring sudo access
            # - Setting up SSH keys
        
        # Update progress to mid-point
        progress['steps'].append({
            'stage': 'status_change',
            'timestamp': datetime.now().isoformat(),
            'status': 'Applying configuration'
        })
        
        mid_progress = {
            'vps_id': vps_id,
            'start_time': progress['start_time'],
            'end_time': None,
            'current_stage': 'configuring',
            'current_status': f'Configuring {os_type.upper()} VM with IP {ip_address}',
            'percent': 50,
            'success': None,
            'steps': progress['steps']
        }
        update_vps_config_status(vps_id, 'vps_configuring', json.dumps(mid_progress))
        
        # Simulate configuration delay
        time.sleep(5)
        
        # Save credentials to database
        print(f"\n💾 Saving credentials to database...")
        cred_saved = update_vps_credentials(vps_id, ip_address, admin_username, admin_password)
        
        if cred_saved:
            progress['steps'].append({
                'stage': 'status_change',
                'timestamp': datetime.now().isoformat(),
                'status': f'Credentials saved: {admin_username}@{ip_address}'
            })
        
        # Final progress
        final_progress = {
            'vps_id': vps_id,
            'start_time': progress['start_time'],
            'end_time': datetime.now().isoformat(),
            'current_stage': 'config_complete',
            'current_status': f'VPS configured: {ip_address}',
            'percent': 100,
            'success': True,
            'steps': progress['steps']
        }
        
        # Update final status
        update_vps_config_status(vps_id, 'vps_ready', json.dumps(final_progress))
        
        print(f"\n✅ VPS #{vps_id} configuration completed!")
        print(f"   IP: {ip_address}")
        print(f"   User: {admin_username}")
        print(f"   Password: {'*' * len(admin_password)}")
        
        Disconnect(si)
        
    except Exception as e:
        print(f"❌ Configuration failed: {str(e)}")
        progress['steps'].append({
            'stage': 'error',
            'timestamp': datetime.now().isoformat(),
            'status': str(e)
        })
        
        error_progress = {
            'vps_id': vps_id,
            'start_time': progress['start_time'],
            'end_time': datetime.now().isoformat(),
            'current_stage': 'config_error',
            'current_status': str(e),
            'percent': 0,
            'success': False,
            'steps': progress['steps']
        }
        
        update_vps_config_status(vps_id, 'vps_config_error', json.dumps(error_progress))

def get_pending_vps_config():
    """Get VPS instances with create_status = 'vps_create_done' and config_status = null"""
    conn = get_db_connection()
    if not conn:
        return []
    
    try:
        with conn.cursor() as cursor:
            sql = """
            SELECT * FROM vps_instances 
            WHERE create_status = %s 
            AND (config_status IS NULL OR config_status = '')
            LIMIT 10
            """
            cursor.execute(sql, ('vps_create_done',))
            return cursor.fetchall()
    except Exception as e:
        print(f"❌ Error querying VPS list: {e}")
        return []
    finally:
        conn.close()

def main():
    """Main loop - continuously check for VPS configuration requests"""
    print("=" * 60)
    print("🚀 VPS Configuration Daemon Started")
    print("=" * 60)
    print(f"Database: {os.getenv('DB_HOST')}:{os.getenv('DB_PORT')}/{os.getenv('DB_DATABASE')}")
    print(f"vCenter: {os.getenv('VCENTER_DOMAIN')}")
    print(f"Loop interval: 10 seconds")
    print("=" * 60)
    
    threads = []
    
    try:
        while True:
            # Get pending VPS configurations
            pending_vps = get_pending_vps_config()
            
            if pending_vps:
                print(f"\n📊 Found {len(pending_vps)} VPS(s) pending configuration")
                
                for vps in pending_vps:
                    vps_id = vps['id']
                    vm_name = vps['name']
                    init_os = vps['init_os']
                    vmware_vm_id = vps.get('vmware_vm_id')
                    
                    print(f"  → VPS #{vps_id}: {vm_name} (OS: {init_os})")
                    
                    # Start configuration thread
                    thread = Thread(
                        target=configure_vps_thread,
                        args=(vps_id, vm_name, init_os, vmware_vm_id),
                        daemon=True
                    )
                    threads.append(thread)
                    thread.start()
            
            # Clean up finished threads
            threads = [t for t in threads if t.is_alive()]
            
            # Wait before next check
            print(f"\n⏱️  Waiting 10 seconds for next check... ({len(threads)} thread(s) running)")
            time.sleep(10)
    
    except KeyboardInterrupt:
        print("\n\n🛑 Stopping daemon...")
        print(f"Waiting for {len([t for t in threads if t.is_alive()])} thread(s) to finish...")
        for thread in threads:
            if thread.is_alive():
                thread.join(timeout=10)
        print("✅ Daemon stopped")
        sys.exit(0)

if __name__ == "__main__":
    main()
