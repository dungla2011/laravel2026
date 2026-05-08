#!/usr/bin/env python3
""" LAD
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

def fetch_all_vm_props(content, host_ip, vm_name=None):
    """Fetch all VM properties in a single PropertyCollector API call"""
    property_paths = [
        'name',
        'config.hardware.numCPU',
        'config.hardware.memoryMB',
        'config.hardware.device',
        'config.uuid',
        'config.instanceUuid',
        'config.guestFullName',
        'runtime.powerState',
        'runtime.connectionState',
        'runtime.bootTime',
        'guest.guestState',
        'guest.hostName',
        'storage.perDatastoreUsage',
    ]

    container = content.viewManager.CreateContainerView(
        content.rootFolder, [vim.VirtualMachine], True
    )

    traversal = vmodl.query.PropertyCollector.TraversalSpec(
        name='containerView',
        type=vim.view.ContainerView,
        path='view',
        skip=False
    )

    obj_spec = vmodl.query.PropertyCollector.ObjectSpec(
        obj=container,
        skip=True,
        selectSet=[traversal]
    )

    prop_spec = vmodl.query.PropertyCollector.PropertySpec(
        type=vim.VirtualMachine,
        pathSet=property_paths
    )

    filter_spec = vmodl.query.PropertyCollector.FilterSpec(
        objectSet=[obj_spec],
        propSet=[prop_spec]
    )

    try:
        result = content.propertyCollector.RetrieveContents([filter_spec])
    finally:
        container.Destroy()

    vms_info = []
    for obj_content in result:
        if not obj_content.propSet:
            continue

        props = {p.name: p.val for p in obj_content.propSet}
        vm_name_val = props.get('name', '')

        if vm_name and vm_name_val != vm_name:
            continue

        # Hardware
        cpu_count = props.get('config.hardware.numCPU', 0) or 0
        memory_mb = props.get('config.hardware.memoryMB', 0) or 0

        # Disk and NIC - single pass
        disk_size_gb = 0
        disk_count = 0
        disk_details = []
        nics = []
        devices = props.get('config.hardware.device', []) or []
        for device in devices:
            if isinstance(device, vim.vm.device.VirtualDisk):
                disk_count += 1
                disk_size = 0
                disk_label = device.deviceInfo.label if device.deviceInfo else f"Disk {disk_count}"
                if hasattr(device, 'capacityInKB') and device.capacityInKB:
                    disk_size = device.capacityInKB / (1024 * 1024)
                elif hasattr(device, 'capacityInBytes') and device.capacityInBytes:
                    disk_size = device.capacityInBytes / (1024 * 1024 * 1024)
                else:
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
            elif isinstance(device, vim.vm.device.VirtualEthernetCard):
                nics.append({
                    'label': device.deviceInfo.label if device.deviceInfo else 'Unknown',
                    'mac_address': device.macAddress,
                    'network_name': device.backing.deviceName if hasattr(device.backing, 'deviceName') else 'Unknown'
                })

        # Fallback disk size from storage if disk is 0
        if disk_size_gb == 0 and props.get('config.uuid'):
            print(f"    ⚠️  Warning: {vm_name_val} - disk_size=0 (found {disk_count} disk devices)")
            for idx, disk in enumerate(disk_details, 1):
                print(f"        Disk {idx}: {disk['label']}, capacityInKB={disk['capacity_kb']}")
            storage_usage = props.get('storage.perDatastoreUsage') or []
            for usage in storage_usage:
                if hasattr(usage, 'committed'):
                    disk_size_gb += usage.committed / (1024 * 1024 * 1024)
            if disk_size_gb > 0:
                print(f"    ✓ Retrieved disk size from storage usage: {disk_size_gb:.2f}GB")

        # Uptime calculation
        boot_time = props.get('runtime.bootTime')
        uptime_info = {'boot_time': None, 'uptime_minutes': None, 'uptime_hours': None, 'uptime_days': None}
        if props.get('guest.hostName') and boot_time:
            try:
                uptime_seconds = (datetime.now(boot_time.tzinfo) - boot_time).total_seconds()
                uptime_minutes = int(uptime_seconds / 60)
                uptime_info = {
                    'boot_time': boot_time.isoformat(),
                    'uptime_minutes': uptime_minutes,
                    'uptime_hours': uptime_minutes // 60,
                    'uptime_days': uptime_minutes // (60 * 24)
                }
            except Exception as e:
                print(f"Error getting uptime for {vm_name_val}: {e}")

        vms_info.append({
            'connection_state': props.get('runtime.connectionState', 'Unknown'),
            'guest_state': props.get('guest.guestState', 'Unknown') or 'Unknown',
            'host_ip': host_ip,
            'name': vm_name_val,
            'vm_id': props.get('config.uuid', 'Unknown') or 'Unknown',
            'bios_uuid': props.get('config.uuid', 'Unknown') or 'Unknown',
            'instance_uuid': props.get('config.instanceUuid', 'Unknown') or 'Unknown',
            'power_state': props.get('runtime.powerState', 'Unknown'),
            'cpu': cpu_count,
            'memory_mb': memory_mb,
            'memory_gb': memory_mb / 1024,
            'disk_gb': round(disk_size_gb, 2),
            'disk_count': disk_count,
            'nics': nics,
            'mac_addresses': [nic['mac_address'] for nic in nics],
            'guest_os': props.get('config.guestFullName', 'Unknown') or 'Unknown',
            'boot_time': uptime_info['boot_time'],
            'uptime_minutes': uptime_info['uptime_minutes'],
            'uptime_hours': uptime_info['uptime_hours'],
            'uptime_days': uptime_info['uptime_days'],
        })

    return vms_info

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

        # Fetch all VM properties in a single PropertyCollector call
        if vm_name:
            thread_safe_print(f"[{get_timestamp()}] 📋 Fetching VM: {vm_name} from {esxi_host}")
        else:
            thread_safe_print(f"[{get_timestamp()}] 📋 Fetching all VMs from {esxi_host}")

        host_vms = fetch_all_vm_props(content, esxi_host, vm_name)

        if vm_name and not host_vms:
            thread_safe_print(f"[{get_timestamp()}] ⚠️  VM '{vm_name}' not found on {esxi_host}")
            Disconnect(si)
            return

        if not vm_name:
            thread_safe_print(f"[{get_timestamp()}] ✅ Found {len(host_vms)} VMs on {esxi_host}\n")
            for vm_info in host_vms:
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
