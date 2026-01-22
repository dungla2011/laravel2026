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

def get_console_ticket(vm, esxi_host):
    """Get console ticket for VM"""
    try:
        # Acquire ticket for console access
        ticket = vm.AcquireTicket('webmks')

        # Use ESXi host if ticket.host is null or empty
        ticket_host = ticket.host if ticket.host else esxi_host

        return {
            'success': True,
            'ticket': ticket.ticket,
#             'host': ticket_host,
            'host': 'sv992.galaxycloud.vn',
#             'port': ticket.port,
             'port': 20001,
            'cfgFile': ticket.cfgFile,
            'sslThumbprint': ticket.sslThumbprint if hasattr(ticket, 'sslThumbprint') else None
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
    esxi_user = None
    esxi_password = None

    for arg in sys.argv[1:]:
        if arg.startswith('vm='):
            vm_name = arg.split('=', 1)[1]
        elif arg.startswith('host='):
            esxi_host = arg.split('=', 1)[1]
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

        # Get VM
        content = si.RetrieveContent()
        vm = get_vm_by_name(content, vm_name)

        if not vm:
            print(json.dumps({
                'success': False,
                'error': f'VM "{vm_name}" not found on {esxi_host}'
            }))
            sys.exit(1)

        # Check if VM is powered on
        if vm.runtime.powerState != 'poweredOn':
            print(json.dumps({
                'success': False,
                'error': f'VM "{vm_name}" is not powered on (current state: {vm.runtime.powerState})'
            }))
            sys.exit(1)

        # Get console ticket
        result = get_console_ticket(vm, esxi_host)

        # Add VM info to result
        if result['success']:
            result['vm_name'] = vm_name
            result['vm_id'] = vm.config.uuid if vm.config else None
            result['power_state'] = vm.runtime.powerState
            # result['esxi_host'] = esxi_host
            result['esxi_host'] = "sv992.galaxycloud.vn:20001"

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
