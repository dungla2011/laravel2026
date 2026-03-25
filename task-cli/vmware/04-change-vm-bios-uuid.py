#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
Change/Update VM BIOS UUID in vCenter
Note: VM must be powered OFF to change BIOS UUID
"""

import sys
import ssl
import argparse
from pyVmomi import vim
from pyVim.connect import SmartConnect, Disconnect

# Config from .env or hardcode
VCENTER_DOMAIN = '192.168.1.2'
VCENTER_UID = 'administrator@vsphere.local'
VCENTER_PW = 'password'


def get_vm_by_name(si, name):
    """Get VM object by name"""
    content = si.RetrieveContent()
    container = content.rootFolder
    viewType = [vim.VirtualMachine]
    recursive = True
    containerView = content.viewManager.CreateContainerView(container, viewType, recursive)
    children = containerView.view
    
    for child in children:
        if child.name == name:
            return child
    
    return None


def get_vm_by_uuid(si, uuid):
    """Get VM by BIOS UUID"""
    try:
        search_index = si.RetrieveContent().searchIndex
        return search_index.FindByUuid(None, uuid, True, True)
    except:
        return None


def get_vm_info(vm):
    """Get VM basic information"""
    if not vm:
        return None
    
    return {
        'name': vm.name,
        'bios_uuid': vm.config.uuid,
        'power_state': vm.runtime.powerState,
        'cpu': vm.config.hardware.numCPU,
        'memory': vm.config.hardware.memoryMB
    }


def change_vm_bios_uuid(si, vm_name, new_uuid):
    """
    Change VM BIOS UUID
    Requires: VM must be powered OFF
    """
    
    print(f"[*] Connecting to vCenter {VCENTER_DOMAIN}...")
    
    # Get VM
    vm = get_vm_by_name(si, vm_name)
    if not vm:
        print(f"[!] VM '{vm_name}' not found")
        return False
    
    info = get_vm_info(vm)
    print(f"\n[*] VM Information:")
    print(f"    Name: {info['name']}")
    print(f"    Current BIOS UUID: {info['bios_uuid']}")
    print(f"    Power State: {info['power_state']}")
    print(f"    CPU: {info['cpu']}, Memory: {info['memory']}MB")
    
    # Check power state
    if info['power_state'] != 'poweredOff':
        print(f"\n[!] ERROR: VM must be powered OFF to change BIOS UUID")
        print(f"    Current state: {info['power_state']}")
        return False
    
    print(f"\n[*] New BIOS UUID: {new_uuid}")
    
    # Create config spec
    config_spec = vim.vm.ConfigSpec()
    config_spec.uuid = new_uuid
    
    try:
        print(f"\n[*] Applying BIOS UUID change...")
        task = vm.ReconfigVM_Task(config_spec)
        
        # Wait for task completion
        while task.info.state not in [vim.TaskInfo.State.success, vim.TaskInfo.State.error]:
            print(".", end="", flush=True)
        
        if task.info.state == vim.TaskInfo.State.success:
            print("\n[✓] SUCCESS: BIOS UUID changed!")
            
            # Verify
            vm_updated = get_vm_by_name(si, vm_name)
            new_info = get_vm_info(vm_updated)
            print(f"\n[*] Updated BIOS UUID: {new_info['bios_uuid']}")
            return True
        else:
            print(f"\n[!] ERROR: {task.info.error.msg}")
            return False
            
    except Exception as e:
        print(f"\n[!] ERROR: {str(e)}")
        return False


def list_vms(si):
    """List all VMs with their BIOS UUIDs"""
    content = si.RetrieveContent()
    container = content.rootFolder
    viewType = [vim.VirtualMachine]
    recursive = True
    containerView = content.viewManager.CreateContainerView(container, viewType, recursive)
    children = containerView.view
    
    print(f"\n{'VM Name':<30} {'BIOS UUID':<40} {'Power State':<15} {'CPU':<5} {'RAM(MB)':<10}")
    print("-" * 100)
    
    for vm in children:
        info = get_vm_info(vm)
        print(f"{info['name']:<30} {info['bios_uuid']:<40} {info['power_state']:<15} {info['cpu']:<5} {info['memory']:<10}")


def main():
    parser = argparse.ArgumentParser(description='Change VM BIOS UUID in vCenter')
    parser.add_argument('--list', action='store_true', help='List all VMs with UUIDs')
    parser.add_argument('--vm-name', type=str, help='VM name to change')
    parser.add_argument('--new-uuid', type=str, help='New BIOS UUID (format: 12345678-1234-1234-1234-123456789012)')
    parser.add_argument('--get-info', type=str, metavar='VM_NAME', help='Get info for specific VM')
    
    args = parser.parse_args()
    
    # SSL config
    context = ssl.create_default_context()
    context.check_hostname = False
    context.verify_mode = ssl.CERT_NONE
    
    # Connect to vCenter
    print(f"[*] Connecting to vCenter: {VCENTER_DOMAIN}")
    try:
        si = SmartConnect(
            host=VCENTER_DOMAIN,
            user=VCENTER_UID,
            pwd=VCENTER_PW,
            sslContext=context,
            port=443
        )
        print("[✓] Connected!")
    except Exception as e:
        print(f"[!] Connection failed: {str(e)}")
        return False
    
    try:
        # List mode
        if args.list:
            print("\n[*] Listing all VMs...")
            list_vms(si)
            return True
        
        # Get info for specific VM
        if args.get_info:
            vm = get_vm_by_name(si, args.get_info)
            if vm:
                info = get_vm_info(vm)
                print(f"\n[*] VM Information:")
                print(f"    Name: {info['name']}")
                print(f"    BIOS UUID: {info['bios_uuid']}")
                print(f"    Power State: {info['power_state']}")
                print(f"    CPU: {info['cpu']}, Memory: {info['memory']}MB")
            else:
                print(f"[!] VM '{args.get_info}' not found")
            return True
        
        # Change BIOS UUID
        if args.vm_name and args.new_uuid:
            return change_vm_bios_uuid(si, args.vm_name, args.new_uuid)
        else:
            parser.print_help()
            print("\n[*] Examples:")
            print("    List all VMs:")
            print("        python 04-change-vm-bios-uuid.py --list")
            print("\n    Get VM info:")
            print("        python 04-change-vm-bios-uuid.py --get-info 'VM-Name'")
            print("\n    Change BIOS UUID (VM must be OFF):")
            print("        python 04-change-vm-bios-uuid.py --vm-name 'VM-Name' --new-uuid '12345678-1234-1234-1234-123456789012'")
            
    finally:
        Disconnect(si)
        print("\n[*] Disconnected from vCenter")


if __name__ == '__main__':
    sys.exit(0 if main() else 1)
