#!/usr/bin/env python3
"""
Clone VMS using pyVmomi and vCenter API
Clone one VPS to another VPS with customizable specs

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

def get_obj(content, vimtype, name=None, folder=None, vm_id=None):
    """
    Get the vsphere object associated with a given text name or VM ID
    """
    if folder:
        container = content.viewManager.CreateContainerView(folder, vimtype, True)
    else:
        container = content.viewManager.CreateContainerView(content.rootFolder, vimtype, True)
    try:
        for c in container.view:
            # Search by VM ID (UUID)
            if vm_id:
                if hasattr(c, 'config') and c.config and c.config.uuid == vm_id:
                    return c
            # Search by name
            elif name:
                if c.name == name:
                    return c
            else:
                return c
    finally:
        container.Destroy()
    return None

def wait_for_task(task, timeout=3600, progress_callback=None):
    """
    Wait for a task to complete
    Returns (success: bool, elapsed_time: float)
    
    Args:
        task: vCenter task object
        timeout: Maximum time to wait in seconds
        progress_callback: Optional function to call with progress updates (progress_dict)
    """
    start_time = time.time()
    last_update = 0
    
    while time.time() - start_time < timeout:
        if task.info.state == vim.TaskInfo.State.success:
            print(f"✅ Task completed successfully")
            return True, time.time() - start_time
        elif task.info.state == vim.TaskInfo.State.error:
            print(f"❌ Task failed: {task.info.error.msg}")
            return False, time.time() - start_time
        
        # Progress update every 5 seconds
        elapsed = int(time.time() - start_time)
        if elapsed - last_update >= 5:
            print(f"⏳ Task running... ({elapsed}s)")
            if progress_callback:
                progress_callback(
                    'cloning',
                    f'⏳ Clone operation in progress ({elapsed}s)',
                    getattr(task.info, 'progress', 0) or 0
                )
            last_update = elapsed
        
        time.sleep(1)
    
    print(f"❌ Task timeout after {timeout}s")
    return False, timeout

def clone_vm(source_vm_name=None, source_vm_id=None, dest_vm_name=None, vcenter_host=None, vcenter_user=None, vcenter_password=None, 
             resource_pool=None, datastore=None, folder=None, power_on=False, file_result_json=None, progress_callback=None):
    """
    Clone a VM from source to destination
    
    Args:
        source_vm_name: Name of the source VM to clone (name or ID required)
        source_vm_id: ID/UUID of the source VM (name or ID required)
        dest_vm_name: Name for the new cloned VM
        vcenter_host: vCenter host address
        vcenter_user: vCenter username
        vcenter_password: vCenter password
        resource_pool: Name of resource pool (optional)
        datastore: Name of datastore (optional)
        folder: Name of folder (optional)
        power_on: Whether to power on the cloned VM (default: False)
        file_result_json: Path to save JSON result (optional)
        progress_callback: Function to call with progress updates (optional)
    
    Returns:
        dict with clone status and details
    """
    
    result = {
        'success': False,
        'source_vm_name': source_vm_name,
        'source_vm_id': source_vm_id,
        'percent': 0,
        'dest_vm': dest_vm_name,
        'dest_vm_id': None,
        'new_id': None,
        'new_vm_id': None,
        'message': '',
        'error': '',
        'task_id': None,
        'progress': [],
        'file_result_json': file_result_json
    }
    
    def add_progress(stage, status, percent=0):
        """Add progress update to result and save to file"""
        progress_entry = {
            'stage': stage,
            'timestamp': datetime.now().isoformat(),
            'status': status,
            'percent': percent
        }
        result['progress'].append(progress_entry)
        result['percent'] = percent  # Update overall progress
        print(f"  → {status}")
        
        # Write to file if specified
        if file_result_json:
            try:
                with open(file_result_json, 'w') as f:
                    json.dump(result, f, indent=2, default=str)
            except Exception as e:
                print(f"  ⚠️  Failed to write progress file: {e}")
    
    try:
        # Validate inputs
        if not (source_vm_name or source_vm_id):
            result['error'] = "Either source_vm (name) or source_vm_id must be provided"
            print(f"❌ {result['error']}")
            add_progress('validation', f"❌ Error: {result['error']}")
            return result
        
        if not dest_vm_name:
            result['error'] = "dest_vm (destination name) is required"
            print(f"❌ {result['error']}")
            add_progress('validation', f"❌ Error: {result['error']}")
            return result
        
        # Disable SSL warning
        context = ssl._create_unverified_context()
        
        add_progress('connecting', f"🔗 Connecting to vCenter: {vcenter_host}", 10)
        print(f"\n🔗 Connecting to vCenter: {vcenter_host}")
        
        # Connect to vCenter (not ESXi)
        si = SmartConnect(
            host=vcenter_host,
            user=vcenter_user,
            pwd=vcenter_password,
            sslContext=context
        )
        
        if not si:
            result['error'] = f"Failed to connect to vCenter: {vcenter_host}"
            print(f"❌ {result['error']}")
            add_progress('connecting', f"❌ {result['error']}", 0)
            return result
        
        add_progress('connecting', f"✅ Connected to vCenter", 15)
        print(f"✅ Connected to vCenter")
        atexit.register(Disconnect, si)
        
        content = si.RetrieveContent()
        
        # Find source VM by name or ID
        if source_vm_id:
            add_progress('finding_source', f"📋 Finding source VM by ID: {source_vm_id}", 20)
            print(f"\n📋 Finding source VM by ID: {source_vm_id}")
            source_vm = get_obj(content, [vim.VirtualMachine], vm_id=source_vm_id)
        else:
            add_progress('finding_source', f"📋 Finding source VM by name: {source_vm_name}", 20)
            print(f"\n📋 Finding source VM by name: {source_vm_name}")
            source_vm = get_obj(content, [vim.VirtualMachine], name=source_vm_name)
        
        if not source_vm:
            search_by = f"ID: {source_vm_id}" if source_vm_id else f"name: {source_vm_name}"
            result['error'] = f"Source VM ({search_by}) not found"
            print(f"❌ {result['error']}")
            add_progress('finding_source', f"❌ {result['error']}", 0)
            return result
        
        add_progress('finding_source', f"✅ Found source VM: {source_vm.name}", 30)
        print(f"✅ Found source VM")
        print(f"   - Name: {source_vm.name}")
        print(f"   - ID: {source_vm.config.uuid if source_vm.config else 'Unknown'}")
        print(f"   - CPU: {source_vm.config.hardware.numCPU}")
        print(f"   - Memory: {source_vm.config.hardware.memoryMB} MB")
        print(f"   - Guest OS: {source_vm.config.guestFullName}")
        
        # Get or use defaults for clone specs
        rel_spec = vim.vm.RelocateSpec()
        
        # Set resource pool
        if resource_pool:
            rp = get_obj(content, [vim.ResourcePool], resource_pool)
            if rp:
                rel_spec.pool = rp
                print(f"✓ Using resource pool: {resource_pool}")
        else:
            # Use default resource pool (root)
            resource_pools = content.viewManager.CreateContainerView(
                content.rootFolder, 
                [vim.ResourcePool], 
                True
            )
            if resource_pools.view:
                rel_spec.pool = resource_pools.view[0]
                print(f"✓ Using default resource pool")
            resource_pools.Destroy()
        
        # Set datastore
        if datastore:
            ds = get_obj(content, [vim.Datastore], datastore)
            if ds:
                rel_spec.datastore = ds
                print(f"✓ Using datastore: {datastore}")
        else:
            # Use source VM's datastore
            if source_vm.datastore:
                rel_spec.datastore = source_vm.datastore[0]
                print(f"✓ Using source VM datastore: {source_vm.datastore[0].name}")
        
        # Create clone spec
        clone_spec = vim.vm.CloneSpec()
        clone_spec.location = rel_spec
        clone_spec.powerOn = power_on
        clone_spec.template = False
        
        # Find destination folder
        if folder:
            dest_folder = get_obj(content, [vim.Folder], folder)
            if not dest_folder:
                dest_folder = source_vm.parent
                print(f"⚠️  Folder '{folder}' not found, using source VM folder")
        else:
            dest_folder = source_vm.parent
        
        # Execute clone
        add_progress('cloning', f"🚀 Starting clone: {source_vm.name} → {dest_vm_name}", 40)
        print(f"\n🚀 Starting clone: {source_vm.name} → {dest_vm_name}")
        task = source_vm.Clone(
            folder=dest_folder,
            name=dest_vm_name,
            spec=clone_spec
        )
        
        result['task_id'] = task.info.key
        
        # Wait for clone to complete
        add_progress('cloning', f"⏳ Waiting for clone operation to complete...", 50)
        print(f"⏳ Waiting for clone operation to complete...")
        success, elapsed_time = wait_for_task(task, timeout=3600, progress_callback=add_progress)
        
        if success:
            add_progress('cloning', f"✅ Clone operation completed ({elapsed_time:.2f}s)", 75)
            print(f"\n✅ Clone completed successfully!")
            print(f"   - Source: {source_vm.name}")
            print(f"   - Destination: {dest_vm_name}")
            print(f"   - Power on: {power_on}")
            
            # Now find the cloned VM and get its ID
            add_progress('finding_new_vm', f"🔍 Finding cloned VM: {dest_vm_name}", 80)
            print(f"\n🔍 Finding cloned VM: {dest_vm_name}")
            
            # Give vCenter a moment to fully register the new VM
            time.sleep(2)
            
            # Try to find the new VM
            new_vm = None
            for attempt in range(5):
                new_vm = get_obj(content, [vim.VirtualMachine], name=dest_vm_name)
                if new_vm:
                    break
                print(f"  ⏳ Attempt {attempt+1}/5 to find new VM...")
                time.sleep(2)
            
            if new_vm and new_vm.config:
                result['new_vm_id'] = new_vm.config.uuid
                result['dest_vm_id'] = new_vm.config.uuid
                result['new_id'] = new_vm.config.uuid
                add_progress('finding_new_vm', f"✅ Found cloned VM ID: {new_vm.config.uuid}", 100)
                print(f"✅ Found cloned VM")
                print(f"   - Name: {new_vm.name}")
                print(f"   - ID: {new_vm.config.uuid}")
            else:
                add_progress('finding_new_vm', f"⚠️  Could not find new VM ID (VM may not be fully registered yet)", 95)
                print(f"⚠️  Could not find new VM UUID (VM may not be fully registered yet)")
            
            result['success'] = True
            result['message'] = f"VM cloned successfully: {dest_vm_name}"
        else:
            result['error'] = "Clone operation failed or timed out"
            add_progress('cloning', f"❌ {result['error']}")
        
        Disconnect(si)
        
    except vmodl.MethodFault as e:
        result['error'] = f"vSphere API error: {e.msg}"
        print(f"❌ {result['error']}")
        add_progress('error', f"❌ {result['error']}")
    except Exception as e:
        result['error'] = str(e)
        print(f"❌ Error: {result['error']}")
        add_progress('error', f"❌ {result['error']}")
    
    return result

def main():
    """
    Main function - parse arguments and clone VM
    
    Usage:
        python clone_vm_pyVmomi.py source_vm=<name|id> dest_vm=<name> [options]
    
    Options:
        source_vm=<name|id>       Name or ID (UUID) of source VM to clone (required)
        dest_vm=<name>            Name for cloned VM (required)
        resource_pool=<name>      Resource pool name (optional)
        datastore=<name>          Datastore name (optional)
        folder=<name>             Destination folder (optional)
        power_on=true|false       Power on clone (default: false)
        file_result_json=<file>   Save detailed result to JSON file (optional)
        output=<file>             Deprecated, use file_result_json instead (optional)
    """
    
    # Get vCenter credentials from environment or use defaults
    vcenter_host = os.getenv('VCENTER_DOMAIN')
    vcenter_user = os.getenv('VCENTER_UID')
    vcenter_password = os.getenv('VCENTER_PW')
    
    if not all([vcenter_host, vcenter_user, vcenter_password]):
        print("❌ Missing vCenter credentials in environment variables")
        print("   Set: VCENTER_DOMAIN, VCENTER_UID, VCENTER_PW")
        sys.exit(1)
    
    # Parse command line arguments
    source_vm_input = None  # Can be name or ID
    dest_vm = None
    resource_pool = None
    datastore = None
    folder = None
    power_on = False
    output_file = None
    file_result_json = None
    
    for arg in sys.argv[1:]:
        if arg.startswith('source_vm='):
            source_vm_input = arg.split('=', 1)[1]
        elif arg.startswith('dest_vm='):
            dest_vm = arg.split('=', 1)[1]
        elif arg.startswith('resource_pool='):
            resource_pool = arg.split('=', 1)[1]
        elif arg.startswith('datastore='):
            datastore = arg.split('=', 1)[1]
        elif arg.startswith('folder='):
            folder = arg.split('=', 1)[1]
        elif arg.startswith('power_on='):
            power_on = arg.split('=', 1)[1].lower() == 'true'
        elif arg.startswith('output='):
            output_file = arg.split('=', 1)[1]
        elif arg.startswith('file_result_json='):
            file_result_json = arg.split('=', 1)[1]
    
    # Validate required arguments
    if not source_vm_input or not dest_vm:
        print("❌ Missing required arguments!")
        print("\nUsage: python clone_vm_pyVmomi.py source_vm=<name|id> dest_vm=<name> [options]")
        print("\nOptions:")
        print("  source_vm=<name|id>       Name or UUID of source VM (required)")
        print("  dest_vm=<name>            Name for cloned VM (required)")
        print("  resource_pool=<name>      Resource pool name (optional)")
        print("  datastore=<name>          Datastore name (optional)")
        print("  folder=<name>             Destination folder (optional)")
        print("  power_on=true|false       Power on clone (default: false)")
        print("  file_result_json=<file>   Save detailed result to JSON file (optional)")
        print("\nExamples:")
        print("  # Clone by VM name")
        print("  python clone_vm_pyVmomi.py source_vm=VC93s dest_vm=VC93s-clone")
        print("  # Clone by VM ID (UUID)")
        print("  python clone_vm_pyVmomi.py source_vm=423705f4-9bce-dcb3-7c0a-873bb7e34da6 dest_vm=VC93s-clone")
        print("  # Clone with options")
        print("  python clone_vm_pyVmomi.py source_vm=VC93s dest_vm=VC93s-clone power_on=true")
        print("  # Clone with JSON output file")
        print("  python clone_vm_pyVmomi.py source_vm=VC93s dest_vm=VC93s-clone file_result_json=/tmp/clone_result.json")
        sys.exit(1)
    
    # Determine if input is UUID or name (UUID typically has hyphens)
    source_vm_name = None
    source_vm_id = None
    
    if '-' in source_vm_input and len(source_vm_input) == 36:
        # Looks like UUID format
        source_vm_id = source_vm_input
        print(f"📌 Source VM identified as UUID")
    else:
        # Treat as name
        source_vm_name = source_vm_input
        print(f"📌 Source VM identified as name")
    
    print("=" * 60)
    print("🔧 VM Clone Script (pyVmomi)")
    print("=" * 60)
    print(f"vCenter: {vcenter_host}")
    if source_vm_name:
        print(f"Source VM: {source_vm_name}")
    else:
        print(f"Source VM ID: {source_vm_id}")
    print(f"Destination VM: {dest_vm}")
    if resource_pool:
        print(f"Resource Pool: {resource_pool}")
    if datastore:
        print(f"Datastore: {datastore}")
    if folder:
        print(f"Folder: {folder}")
    print(f"Power On: {power_on}")
    if file_result_json:
        print(f"Result JSON: {file_result_json}")
    print("=" * 60)
    
    # Execute clone
    start_time = time.time()
    result = clone_vm(
        source_vm_name=source_vm_name,
        source_vm_id=source_vm_id,
        dest_vm_name=dest_vm,
        vcenter_host=vcenter_host,
        vcenter_user=vcenter_user,
        vcenter_password=vcenter_password,
        resource_pool=resource_pool,
        datastore=datastore,
        folder=folder,
        power_on=power_on,
        file_result_json=file_result_json
    )
    
    # Calculate execution time
    elapsed_time = time.time() - start_time
    result['execution_time_seconds'] = round(elapsed_time, 2)
    
    # Output results
    print("\n" + "=" * 60)
    print("📊 Clone Result")
    print("=" * 60)
    
    if result['success']:
        print(f"✅ Status: SUCCESS")
        print(f"   {result['message']}")
        if result['new_vm_id']:
            print(f"   New VM ID: {result['new_vm_id']}")
    else:
        print(f"❌ Status: FAILED")
        print(f"   Error: {result['error']}")
    
    print(f"⏱️  Execution time: {result['execution_time_seconds']}s")
    print("=" * 60)
    
    # Save to file if requested
    if file_result_json:
        try:
            with open(file_result_json, 'w') as f:
                json.dump(result, f, indent=2, default=str)
            print(f"✅ Result saved to: {file_result_json}")
        except Exception as e:
            print(f"❌ Error writing to {file_result_json}: {e}")
    elif output_file:
        try:
            with open(output_file, 'w') as f:
                json.dump(result, f, indent=2, default=str)
            print(f"✅ Result saved to: {output_file}")
        except Exception as e:
            print(f"❌ Error writing to {output_file}: {e}")
    
    # Print JSON result
    print("\n📋 JSON Result:")
    print(json.dumps(result, indent=2, default=str))
    
    sys.exit(0 if result['success'] else 1)

if __name__ == "__main__":
    main()
