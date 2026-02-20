#!/usr/bin/env python3
"""
Get VMware console ticket for VM remote console access
Usage: python get_console_ticket.py vm=<vm_name> host=<esxi_host>
"""

import ssl
import sys
import json
import os
from pyVmomi import vim
from pyVim.connect import SmartConnect, Disconnect
import atexit
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

# Domain/IP to Console Domain:Port mapping
DOMAIN_CONSOLE_MAP = {
    '103.74.121.166': 'sv992.galaxycloud.vn:20001',
    '192.168.20.11': 'sv992.galaxycloud.vn:20001',
    '10.0.1.11': 'sv992.galaxycloud.vn:20001',
    
    '103.74.121.196': 'sv992.galaxycloud.vn:20002',
    '192.168.20.12': 'sv992.galaxycloud.vn:20002',
    '10.0.1.12': 'sv992.galaxycloud.vn:20002',
    
    '103.74.121.39': 'sv992.galaxycloud.vn:20000',
    '192.168.20.13': 'sv992.galaxycloud.vn:20000',
    '10.0.1.13': 'sv992.galaxycloud.vn:20000',
    
    '10.0.1.99': 'sv992.galaxycloud.vn:20004',
    '10.0.1.14': 'sv992.galaxycloud.vn:20005',
    '10.0.1.15': 'sv992.galaxycloud.vn:20015',
    '10.0.1.16': 'sv992.galaxycloud.vn:20016',
    '10.0.1.17': 'sv992.galaxycloud.vn:20017',
    '10.0.1.18': 'sv992.galaxycloud.vn:20018',
    '10.0.1.19': 'sv992.galaxycloud.vn:20019',
    '10.0.1.20': 'sv992.galaxycloud.vn:20020',
    '10.0.1.21': 'sv992.galaxycloud.vn:20021',
    '10.0.1.31': 'sv992.galaxycloud.vn:20031',
    '10.0.1.22': 'sv992.galaxycloud.vn:20022',
}

def get_vm_by_name(content, vm_name):
    """Find VM by name"""
    container = content.viewManager.CreateContainerView(
        content.rootFolder, [vim.VirtualMachine], True
    )
    for vm in container.view:
        if vm.name == vm_name:
            container.Destroy()
            return vm
    container.Destroy()
    return None

def get_vm_by_id(content, vm_id):
    """Find VM by VM ID (bios_uuid or instance_uuid)"""
    container = content.viewManager.CreateContainerView(
        content.rootFolder, [vim.VirtualMachine], True
    )
    for vm in container.view:
        # Check bios_uuid (config.uuid)
        if vm.config and vm.config.uuid == vm_id:
            container.Destroy()
            return vm
        # Check instance_uuid
        if vm.config and vm.config.instanceUuid == vm_id:
            container.Destroy()
            return vm
    container.Destroy()
    return None

def get_vm_by_uuid(content, vm_uuid):
    """Find VM by UUID (alias for get_vm_by_id)"""
    return get_vm_by_id(content, vm_uuid)

def get_console_ticket(vm, esxi_host):
    """Get console ticket for VM"""
    try:
        # Acquire ticket for console access
        ticket = vm.AcquireTicket('webmks')

        # Map ESXi host to console domain:port
        console_host = DOMAIN_CONSOLE_MAP.get(esxi_host)
        if not console_host:
            # Fallback: use ticket.host if mapping not found
            console_host = ticket.host if ticket.host else esxi_host

        return {
            'success': True,
            'ticket': ticket.ticket,
            'host': console_host,
            'cfgFile': ticket.cfgFile,
            'sslThumbprint': ticket.sslThumbprint if hasattr(ticket, 'sslThumbprint') else None
        }
    except Exception as e:
        return {
            'success': False,
            'error': str(e)
        }

def power_on_vm(vm):
    """Power on VM"""
    try:
        if vm.runtime.powerState == 'poweredOn':
            return {
                'success': True,
                'message': 'VM is already powered on'
            }
        task = vm.PowerOnVM_Task()
        return {
            'success': True,
            'message': 'Power ON command sent',
            'task_id': task.key
        }
    except Exception as e:
        return {
            'success': False,
            'error': str(e)
        }

def power_off_vm(vm):
    """Power off VM"""
    try:
        if vm.runtime.powerState == 'poweredOff':
            return {
                'success': True,
                'message': 'VM is already powered off'
            }
        task = vm.PowerOffVM_Task()
        return {
            'success': True,
            'message': 'Power OFF command sent',
            'task_id': task.key
        }
    except Exception as e:
        return {
            'success': False,
            'error': str(e)
        }

def reset_vm(vm):
    """Reset VM"""
    try:
        if vm.runtime.powerState != 'poweredOn':
            return {
                'success': False,
                'error': 'VM must be powered on to reset'
            }
        task = vm.ResetVM_Task()
        return {
            'success': True,
            'message': 'Reset command sent',
            'task_id': task.key
        }
    except Exception as e:
        return {
            'success': False,
            'error': str(e)
        }

def main():
    # Parse command line arguments
    vm_name = None
    esxi_host = None
    vm_type = 'name'  # Default to 'name', can be 'id' or 'uuid'
    cmd = None  # poweron, poweroff, reset, or None for ticket only
    esxi_user = None
    esxi_password = None

    for arg in sys.argv[1:]:
        if arg.startswith('vm='):
            vm_name = arg.split('=', 1)[1]
        elif arg.startswith('host='):
            esxi_host = arg.split('=', 1)[1]
        elif arg.startswith('vm_type='):
            vm_type = arg.split('=', 1)[1]
        elif arg.startswith('cmd='):
            cmd = arg.split('=', 1)[1]
        elif arg.startswith('user='):
            esxi_user = arg.split('=', 1)[1]
        elif arg.startswith('password='):
            esxi_password = arg.split('=', 1)[1]

    # Get credentials from env if not provided
    if not esxi_user:
        esxi_user = os.getenv('ES_USER')

    # Determine password based on host
    if not esxi_password:
        if esxi_host == '10.0.1.19':
            esxi_password = os.getenv('ES_PW2') + '098#'
        else:
            esxi_password = os.getenv('ES_PW') + '6868'

    # Validate inputs
    if not vm_name or not esxi_host:
        print(json.dumps({
            'success': False,
            'error': 'Missing required parameters: vm and host'
        }))
        sys.exit(1)

    if vm_type not in ['name', 'id', 'uuid']:
        print(json.dumps({
            'success': False,
            'error': f'Invalid vm_type: {vm_type}. Must be: name, id, or uuid'
        }))
        sys.exit(1)

    try:
        # Disable SSL verification
        context = ssl._create_unverified_context()

        # Connect to ESXi
        si = SmartConnect(
            host=esxi_host,
            user=esxi_user,
            pwd=esxi_password,
            sslContext=context,
            connectionPoolTimeout=30
        )

        if not si:
            print(json.dumps({
                'success': False,
                'error': f'Failed to connect to {esxi_host}'
            }))
            sys.exit(1)

        atexit.register(Disconnect, si)

        # Get VM based on type
        content = si.RetrieveContent()
        vm = None

        if vm_type == 'name':
            vm = get_vm_by_name(content, vm_name)
            search_label = f'name "{vm_name}"'
        elif vm_type in ['id', 'uuid']:
            vm = get_vm_by_id(content, vm_name)
            search_label = f'{vm_type} "{vm_name}"'

        if not vm:
            print(json.dumps({
                'success': False,
                'error': f'VM with {search_label} not found on {esxi_host}'
            }))
            sys.exit(1)

        # Execute command if specified
        if cmd:
            if cmd == 'poweron':
                result = power_on_vm(vm)
            elif cmd == 'poweroff':
                result = power_off_vm(vm)
            elif cmd == 'reset':
                result = reset_vm(vm)
            else:
                result = {
                    'success': False,
                    'error': f'Unknown command: {cmd}. Supported: poweron, poweroff, reset'
                }
        else:
            # Check if VM is powered on (only for console ticket)
            if vm.runtime.powerState != 'poweredOn':
                print(json.dumps({
                    'success': False,
                    'error': f'VM with {search_label} is not powered on (current state: {vm.runtime.powerState})'
                }))
                sys.exit(1)

            # Get console ticket
            result = get_console_ticket(vm, esxi_host)

            # Add VM info to result
            if result['success']:
                result['vm_name'] = vm.name
                result['vm_id'] = vm.config.uuid if vm.config else None
                result['power_state'] = vm.runtime.powerState
                result['esxi_host'] = result['host']

        print(json.dumps(result, indent=2))

        Disconnect(si)

    except Exception as e:
        print(json.dumps({
            'success': False,
            'error': str(e)
        }))
        sys.exit(1)

if __name__ == '__main__':
    main()
