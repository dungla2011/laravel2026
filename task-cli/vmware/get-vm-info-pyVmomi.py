#!/usr/bin/env python3
"""
Connect to ESXi host directly using pyVmomi
Get VM information including boot time, uptime, etc.

Install: pip install pyvmomi
"""

import atexit
import ssl
from pyVmomi import vim, vmodl
from pyVim.connect import SmartConnect, Disconnect
import sys
from datetime import datetime
import json
import os
import time
from dotenv import load_dotenv
from threading import Thread, Lock

# Global lock for thread-safe operations
results_lock = Lock()
print_lock = Lock()

# Load environment variables from .env
load_dotenv()

def get_timestamp():
    """Get current timestamp in Y-m-d H:i:s format"""
    return datetime.now().strftime("%Y-%m-%d %H:%M:%S")

def thread_safe_print(msg):
    """Thread-safe print to console"""
    with print_lock:
        print(msg)

def get_obj(content, vimtype, name=None):
    """
    Get the vsphere object associated with a given text name
    """
    container = content.viewManager.CreateContainerView(content.rootFolder, vimtype, True)
    try:
        for c in container.view:
            if name:
                if c.name == name:
                    return c
            else:
                return c
    finally:
        container.Destroy()
    return None

def get_all_vms(content):
    """Get all VMs from ESXi host"""
    container = content.viewManager.CreateContainerView(content.rootFolder, [vim.VirtualMachine], True)
    vms = [vm for vm in container.view]
    container.Destroy()
    return vms

def get_vm_uptime(vm):
    """
    Calculate VM uptime in seconds
    Returns uptime in minutes
    """
    try:
        # Get VM boot time from guest info
        if vm.guest and vm.guest.hostName:
            # Get last boot time from VM runtime info
            if hasattr(vm.runtime, 'bootTime'):
                boot_time = vm.runtime.bootTime
                if boot_time:
                    uptime_seconds = (datetime.now(boot_time.tzinfo) - boot_time).total_seconds()
                    uptime_minutes = int(uptime_seconds / 60)
                    return {
                        'boot_time': boot_time.isoformat() if boot_time else None,
                        'uptime_minutes': uptime_minutes,
                        'uptime_hours': uptime_minutes // 60,
                        'uptime_days': uptime_minutes // (60 * 24)
                    }
    except Exception as e:
        print(f"Error getting uptime: {e}")

    return {
        'boot_time': None,
        'uptime_minutes': None,
        'uptime_hours': None,
        'uptime_days': None
    }

def get_vm_info(vm, host_ip=None):
    """Get detailed VM information"""
    try:
        # Check if VM has running tasks
        vm_tasks = []
        try:
            if hasattr(vm, 'recentTask') and vm.recentTask:
                for task in vm.recentTask:
                    if hasattr(task, 'info'):
                        task_info = task.info
                        if hasattr(task_info, 'state') and task_info.state not in ['success', 'error']:
                            vm_tasks.append({
                                'name': task_info.name if hasattr(task_info, 'name') else 'Unknown',
                                'state': task_info.state if hasattr(task_info, 'state') else 'Unknown',
                                'progress': task_info.progress if hasattr(task_info, 'progress') else None
                            })
        except:
            pass

        # Refresh VM state to avoid stale data
        try:
            vm.Reload()  # Reload entire VM object including config
        except:
            pass

        try:
            vm.RefreshRuntime()
        except:
            pass  # Some ESXi versions don't support this

        uptime_info = get_vm_uptime(vm)

        # Get hardware info
        cpu_count = vm.config.hardware.numCPU if vm.config else 0
        memory_mb = vm.config.hardware.memoryMB if vm.config else 0

        # Get disk size - with retry and better error handling
        disk_size_gb = 0
        disk_count = 0
        disk_details = []
        if vm.config and vm.config.hardware.device:
            for device in vm.config.hardware.device:
                if isinstance(device, vim.vm.device.VirtualDisk):
                    disk_count += 1
                    disk_size = 0
                    disk_label = device.deviceInfo.label if device.deviceInfo else f"Disk {disk_count}"

                    # Try multiple methods to get disk size
                    if hasattr(device, 'capacityInKB') and device.capacityInKB:
                        disk_size = device.capacityInKB / (1024 * 1024)
                    elif hasattr(device, 'capacityInBytes') and device.capacityInBytes:
                        disk_size = device.capacityInBytes / (1024 * 1024 * 1024)
                    else:
                        # Try to get size from backing file
                        try:
                            if hasattr(device, 'backing') and device.backing:
                                if hasattr(device.backing, 'capacityInKB') and device.backing.capacityInKB:
                                    disk_size = device.backing.capacityInKB / (1024 * 1024)
                                elif hasattr(device.backing, 'capacityInBytes') and device.backing.capacityInBytes:
                                    disk_size = device.backing.capacityInBytes / (1024 * 1024 * 1024)
                        except:
                            pass

                    disk_size_gb += disk_size
                    disk_details.append({
                        'label': disk_label,
                        'size_gb': round(disk_size, 2),
                        'capacity_kb': device.capacityInKB if hasattr(device, 'capacityInKB') else None
                    })

        # If disk is 0 but VM has config, log warning with details
        if disk_size_gb == 0 and vm.config and disk_count > 0:
            print(f"    ⚠️  Warning: {vm.name} - disk_size=0 (found {disk_count} disk devices)")
            for idx, disk in enumerate(disk_details, 1):
                print(f"        Disk {idx}: {disk['label']}, capacityInKB={disk['capacity_kb']}")

            # Check for running tasks
            if vm_tasks:
                print(f"    🔧 VM has {len(vm_tasks)} running task(s):")
                for task in vm_tasks:
                    progress_str = f" ({task['progress']}%)" if task['progress'] is not None else ""
                    print(f"        - {task['name']}: {task['state']}{progress_str}")

            # Try alternative method - storage info
            try:
                if hasattr(vm, 'storage') and vm.storage and hasattr(vm.storage, 'perDatastoreUsage'):
                    for usage in vm.storage.perDatastoreUsage:
                        if hasattr(usage, 'committed'):
                            disk_size_gb += usage.committed / (1024 * 1024 * 1024)
                    if disk_size_gb > 0:
                        print(f"    ✓ Retrieved disk size from storage usage: {disk_size_gb:.2f}GB")
            except Exception as e:
                print(f"    ⚠️  Could not get storage usage: {e}")
        elif disk_size_gb == 0 and vm.config:
            print(f"    ⚠️  Warning: {vm.name} - disk_size=0 (found {disk_count} disk devices)")
            # Try alternative method - storage info
            try:
                if hasattr(vm, 'storage') and vm.storage and hasattr(vm.storage, 'perDatastoreUsage'):
                    for usage in vm.storage.perDatastoreUsage:
                        if hasattr(usage, 'committed'):
                            disk_size_gb += usage.committed / (1024 * 1024 * 1024)
                    if disk_size_gb > 0:
                        print(f"    ✓ Retrieved disk size from storage usage: {disk_size_gb:.2f}GB")
            except Exception as e:
                print(f"    ⚠️  Could not get storage usage: {e}")

        # Get network info
        nics = []
        if vm.config and vm.config.hardware.device:
            for device in vm.config.hardware.device:
                if isinstance(device, vim.vm.device.VirtualEthernetCard):
                    nics.append({
                        'label': device.deviceInfo.label if device.deviceInfo else 'Unknown',
                        'mac_address': device.macAddress,
                        'network_name': device.backing.deviceName if hasattr(device.backing, 'deviceName') else 'Unknown'
                    })

        return {
            'connection_state': vm.runtime.connectionState if hasattr(vm.runtime, 'connectionState') else 'Unknown',
            'guest_state': vm.guest.guestState if vm.guest and hasattr(vm.guest, 'guestState') else 'Unknown',
            'host_ip': host_ip,
            'name': vm.name,
            'vm_id': vm.config.uuid if vm.config else 'Unknown',
            'bios_uuid': vm.config.uuid if vm.config else 'Unknown',
            'instance_uuid': vm.config.instanceUuid if vm.config else 'Unknown',
            'power_state': vm.runtime.powerState,
            'cpu': cpu_count,
            'memory_mb': memory_mb,
            'memory_gb': memory_mb / 1024,
            'disk_gb': round(disk_size_gb, 2),
            'disk_count': disk_count,
            'nics': nics,
            'mac_addresses': [nic['mac_address'] for nic in nics],
            'guest_os': vm.config.guestFullName if vm.config else 'Unknown',
            'boot_time': uptime_info['boot_time'],
            'uptime_minutes': uptime_info['uptime_minutes'],
            'uptime_hours': uptime_info['uptime_hours'],
            'uptime_days': uptime_info['uptime_days'],
        }
    except Exception as e:
        print(f"Error getting VM info for {vm.name}: {e}")
        return None

def fetch_vms_from_host(host_config, vm_name, all_vms_info, results_lock):
    """
    Worker function to fetch VMs from a single ESXi host
    Thread-safe appends to all_vms_info
    """
    esxi_host = host_config['host']
    esxi_user = host_config['user']
    esxi_password = host_config['password']

    if not all([esxi_host, esxi_user, esxi_password]):
        thread_safe_print(f"[{get_timestamp()}] ⚠️  Skipping {esxi_host}: Missing credentials")
        return

    try:
        # Disable SSL warning
        context = ssl._create_unverified_context()

        thread_safe_print(f"[{get_timestamp()}] 🔗 Connecting to ESXi host: {esxi_host}")

        # Connect to ESXi host with timeout
        si = SmartConnect(
            host=esxi_host,
            user=esxi_user,
            pwd=esxi_password,
            sslContext=context,
            connectionPoolTimeout=60  # Add explicit timeout
        )

        if not si:
            thread_safe_print(f"[{get_timestamp()}] ❌ Failed to connect to {esxi_host}")
            return

        thread_safe_print(f"[{get_timestamp()}] ✅ Connected to {esxi_host}")

        content = si.RetrieveContent()

        # Force refresh to avoid stale data
        try:
            content.propertyCollector.RefreshProperties()
        except:
            pass  # Ignore if not supported

        host_vms = []  # Collect VMs for this host

        # Get VMs from this host
        if vm_name:
            thread_safe_print(f"[{get_timestamp()}] 📋 Fetching VM: {vm_name} from {esxi_host}")
            vm = None
            for v in get_all_vms(content):
                if v.name == vm_name:
                    vm = v
                    break

            if not vm:
                thread_safe_print(f"[{get_timestamp()}] ⚠️  VM '{vm_name}' not found on {esxi_host}")
                Disconnect(si)
                return

            vm_info = get_vm_info(vm, host_ip=esxi_host)
            if vm_info:
                host_vms.append(vm_info)
        else:
            thread_safe_print(f"[{get_timestamp()}] 📋 Fetching all VMs from {esxi_host}")
            vms = get_all_vms(content)
            thread_safe_print(f"[{get_timestamp()}] ✅ Found {len(vms)} VMs on {esxi_host}\n")

            for vm in vms:
                vm_info = get_vm_info(vm, host_ip=esxi_host)

                if vm_info:
                    host_vms.append(vm_info)
                    disk_info = f"Disk: {vm_info['disk_gb']}GB ({vm_info['disk_count']} disks, host= {vm_info['host_ip']})" if vm_info.get('disk_count') else f"Disk: {vm_info['disk_gb']}GB"
                    thread_safe_print(f"[{get_timestamp()}]   ✓ {vm_info['name']} - {vm_info['power_state']} - CPU: {vm_info['cpu']} - RAM: {vm_info['memory_gb']:.1f}GB - {disk_info}")
                    if vm_info['uptime_minutes'] is not None:
                        thread_safe_print(f"[{get_timestamp()}]     ⏱️  Uptime: {vm_info['uptime_days']}d {vm_info['uptime_hours']}h")

        # Thread-safe append to shared list
        with results_lock:
            all_vms_info.extend(host_vms)
            thread_safe_print(f"[{get_timestamp()}] ✓ Thread finished for {esxi_host} ({len(host_vms)} VMs)")

        Disconnect(si)

    except vmodl.MethodFault as e:
        thread_safe_print(f"[{get_timestamp()}] ❌ vSphere API error on {esxi_host}: {e.msg}")
    except Exception as e:
        thread_safe_print(f"[{get_timestamp()}] ❌ Error connecting to {esxi_host}: {e}")

def main():
    # Start timing
    start_time = time.time()

    # ESXi hosts configuration - list all hosts
    esxi_hosts = [
        {
            'host': "10.0.1.11",
            'user': os.getenv('ES_USER'),
            'password': os.getenv('ES_PW') + '6868'
        },
        {
            'host': "10.0.1.12",
            'user': os.getenv('ES_USER'),
            'password': os.getenv('ES_PW') + '6868'
        },
        {
            'host': "10.0.1.13",
            'user': os.getenv('ES_USER'),
            'password': os.getenv('ES_PW') + '6868'
        },
        {
            'host': "10.0.1.18",
            'user': os.getenv('ES_USER'),
            'password': os.getenv('ES_PW') + '6868'
        },
        {
            'host': "10.0.1.19",
            'user': os.getenv('ES_USER'),
            'password': os.getenv('ES_PW2') + '098#'
        }
    ]

    # Optional: get specific VM name from command line or env
    vm_name = None
    output_file = None

    # Parse command line arguments
    for arg in sys.argv[1:]:
        if arg.startswith('vm='):
            vm_name = arg.split('=', 1)[1]
        elif arg.startswith('output='):
            output_file = arg.split('=', 1)[1]

    # Fallback to env variables
    if not vm_name:
        vm_name = os.getenv('VM_NAME')

    # Collect all VMs from all hosts
    all_vms_info = []

    try:
        # Create threads for each host
        threads = []

        print(f"[{get_timestamp()}] 🚀 Starting {len(esxi_hosts)} threads to fetch VMs from all hosts...\n")

        for host_config in esxi_hosts:
            thread = Thread(
                target=fetch_vms_from_host,
                args=(host_config, vm_name, all_vms_info, results_lock),
                daemon=False
            )
            threads.append(thread)
            thread.start()

        # Wait for all threads to complete
        for thread in threads:
            thread.join()

        print(f"\n[{get_timestamp()}] ✅ All threads completed")

        # Output results
        if all_vms_info:
            print(f"[{get_timestamp()}] " + "=" * 80)
            print(f"[{get_timestamp()}] 📊 SUMMARY: Total VMs collected: {len(all_vms_info)} from {len(esxi_hosts)} hosts")
            print(f"[{get_timestamp()}] " + "=" * 80)

            json_output = json.dumps(all_vms_info, indent=2, default=str)

            # Save to file if output parameter provided
            if output_file:
                try:
                    with open(output_file, 'w') as f:
                        f.write(json_output)
                    print(f"[{get_timestamp()}] ✅ Output saved to: {output_file}")
                except Exception as e:
                    print(f"[{get_timestamp()}] ❌ Error writing to {output_file}: {e}")
                    print(f"[{get_timestamp()}] Printing to console instead:")
                    print(json_output)
            else:
                print(json_output)
        else:
            print(f"[{get_timestamp()}] ⚠️  No VMs found or unable to connect to any host")

        # Calculate execution time
        end_time = time.time()
        elapsed_time = end_time - start_time
        print(f"[{get_timestamp()}] " + "=" * 60)
        print(f"[{get_timestamp()}] ⏱️  Execution time: {elapsed_time:.2f} seconds")
        print(f"[{get_timestamp()}] " + "=" * 60)

    except Exception as e:
        print(f"❌ Error: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()
