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

# Load environment variables from .env
load_dotenv()

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
        uptime_info = get_vm_uptime(vm)
        
        # Get hardware info
        cpu_count = vm.config.hardware.numCPU if vm.config else 0
        memory_mb = vm.config.hardware.memoryMB if vm.config else 0
        
        # Get disk size
        disk_size_gb = 0
        if vm.config and vm.config.hardware.device:
            for device in vm.config.hardware.device:
                if isinstance(device, vim.vm.device.VirtualDisk):
                    disk_size_gb += device.capacityInKB / (1024 * 1024)
        
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
        for host_config in esxi_hosts:
            esxi_host = host_config['host']
            esxi_user = host_config['user']
            esxi_password = host_config['password']
            
            if not all([esxi_host, esxi_user, esxi_password]):
                print(f"⚠️  Skipping {esxi_host}: Missing credentials")
                continue
            
            try:
                # Disable SSL warning
                context = ssl._create_unverified_context()
                
                print(f"\n🔗 Connecting to ESXi host: {esxi_host}")
                
                # Connect to ESXi host
                si = SmartConnect(
                    host=esxi_host,
                    user=esxi_user,
                    pwd=esxi_password,
                    sslContext=context
                )
                
                if not si:
                    print(f"❌ Failed to connect to {esxi_host}")
                    continue
                
                print(f"✅ Connected to {esxi_host}")
                
                # Ensure we disconnect when done
                atexit.register(Disconnect, si)
                
                content = si.RetrieveContent()
                
                # Get VMs from this host
                if vm_name:
                    print(f"\n📋 Fetching VM: {vm_name} from {esxi_host}")
                    vm = None
                    for v in get_all_vms(content):
                        if v.name == vm_name:
                            vm = v
                            break
                    
                    if not vm:
                        print(f"⚠️  VM '{vm_name}' not found on {esxi_host}")
                        continue
                    
                    vm_info = get_vm_info(vm, host_ip=esxi_host)
                    if vm_info:
                        all_vms_info.append(vm_info)
                else:
                    print(f"\n📋 Fetching all VMs from {esxi_host}")
                    vms = get_all_vms(content)
                    print(f"✅ Found {len(vms)} VMs on {esxi_host}\n")
                    
                    for vm in vms:
                        vm_info = get_vm_info(vm, host_ip=esxi_host)
                        if vm_info:
                            all_vms_info.append(vm_info)
                            print(f"  ✓ {vm_info['name']} - {vm_info['power_state']} - CPU: {vm_info['cpu']} - RAM: {vm_info['memory_gb']:.1f}GB")
                            if vm_info['uptime_minutes'] is not None:
                                print(f"    ⏱️  Uptime: {vm_info['uptime_days']}d {vm_info['uptime_hours']}h")
                
                Disconnect(si)
                
            except vmodl.MethodFault as e:
                print(f"❌ vSphere API error on {esxi_host}: {e.msg}")
                continue
            except Exception as e:
                print(f"❌ Error connecting to {esxi_host}: {e}")
                continue
        
        # Output results
        if all_vms_info:
            print("\n" + "=" * 60)
            print(f"📊 Total VMs collected: {len(all_vms_info)}")
            print("=" * 60)
            
            json_output = json.dumps(all_vms_info, indent=2, default=str)
            
            # Save to file if output parameter provided
            if output_file:
                try:
                    with open(output_file, 'w') as f:
                        f.write(json_output)
                    print(f"✅ Output saved to: {output_file}")
                except Exception as e:
                    print(f"❌ Error writing to {output_file}: {e}")
                    print("Printing to console instead:")
                    print(json_output)
            else:
                print(json_output)
        else:
            print("\n⚠️  No VMs found or unable to connect to any host")
        
        # Calculate execution time
        end_time = time.time()
        elapsed_time = end_time - start_time
        print("\n" + "=" * 60)
        print(f"⏱️  Execution time: {elapsed_time:.2f} seconds")
        print("=" * 60)
    
    except Exception as e:
        print(f"❌ Error: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()
