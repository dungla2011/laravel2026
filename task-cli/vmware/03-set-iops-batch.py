#!/usr/bin/env python3
"""
VPS Batch IOPS Configuration
List VPS instances and set IOPS for disks in batch
Support filtering by VPS name pattern or user email

Usage:
  python 03-set-iops-batch.py --list                    # List all VPS
  python 03-set-iops-batch.py --list --ignore-name "test"  # List VPS (ignore name containing 'test')
  python 03-set-iops-batch.py --list --ignore-email "gmail"  # List VPS (ignore email containing 'gmail')
  python 03-set-iops-batch.py --set-iops 1000           # Set IOPS for all VPS
  python 03-set-iops-batch.py --set-iops 1000 --ignore-name "test"
  python 03-set-iops-batch.py --set-iops 1000 --dry-run  # Preview without making changes
"""

import atexit
import ssl
import sys
import os
import argparse
import time
import json
from datetime import datetime
from dotenv import load_dotenv
import pymysql
from pyVmomi import vim, vmodl
from pyVim.connect import SmartConnect, Disconnect

# Load environment variables
env_path = os.path.join(os.path.dirname(__file__), '../../.env')
load_dotenv(env_path)

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

def get_vps_list(ignore_name=None, ignore_email=None):
    """
    Get list of VPS instances with user info
    
    Args:
        ignore_name: String pattern to ignore VPS names
        ignore_email: String pattern to ignore user emails
    
    Returns:
        List of dicts: [{id, name, user_id, email, bios_uuid, vmware_vm_id, ...}, ...]
    """
    conn = get_db_connection()
    if not conn:
        return []
    
    try:
        with conn.cursor() as cursor:
            sql = """
            SELECT 
                vi.id,
                vi.name,
                vi.user_id,
                vi.bios_uuid,
                vi.vmware_vm_id,
                vi.create_status,
                vi.cpu,
                vi.ram_gb,
                vi.disk_gb,
                u.email,
                u.name as user_name
            FROM vps_instances vi
            LEFT JOIN users u ON vi.user_id = u.id
            WHERE vi.deleted_at IS NULL
            ORDER BY vi.id DESC
            """
            cursor.execute(sql)
            vps_list = cursor.fetchall()
        
        # Filter results
        filtered_list = []
        for vps in vps_list:
            # Skip if name matches ignore pattern
            if ignore_name and ignore_name.lower() in (vps['name'] or '').lower():
                continue
            
            # Skip if email matches ignore pattern
            if ignore_email and ignore_email.lower() in (vps['email'] or '').lower():
                continue
            
            filtered_list.append(vps)
        
        return filtered_list
    
    except Exception as e:
        print(f"❌ Error querying VPS list: {e}")
        return []
    finally:
        conn.close()

def get_obj(content, vimtype, name=None, uuid=None):
    """Get vSphere object by name or UUID"""
    container = content.viewManager.CreateContainerView(content.rootFolder, vimtype, True)
    try:
        for c in container.view:
            if uuid:
                if hasattr(c, 'config') and c.config and c.config.uuid == uuid:
                    return c
            elif name:
                if c.name == name:
                    return c
    finally:
        container.Destroy()
    return None

def get_vm_disks_info(vm):
    """
    Get disk information from VM
    
    Returns:
        List of dicts: [{device_key, name, capacity_gb, iops_limit, ...}, ...]
    """
    disks = []
    
    for device in vm.config.hardware.device:
        if isinstance(device, vim.vm.device.VirtualDisk):
            disk_info = {
                'device_key': device.key,
                'device_name': device.deviceInfo.label if hasattr(device, 'deviceInfo') else f"Disk {len(disks)+1}",
                'capacity_gb': device.capacityInKB / (1024 * 1024),
                'controller_key': device.controllerKey,
                'unit_number': device.unitNumber,
                'backing_file': device.backing.fileName if hasattr(device, 'backing') else 'N/A',
                'iops_limit': None
            }
            
            # Check if IOPS limit is already set
            if hasattr(device, 'storageIOAllocation'):
                if hasattr(device.storageIOAllocation, 'limit'):
                    disk_info['iops_limit'] = device.storageIOAllocation.limit
            
            disks.append(disk_info)
    
    return disks

def set_disk_iops(vm, device_key, iops_limit, dry_run=False):
    """
    Set IOPS limit for a disk
    
    Args:
        vm: VM object
        device_key: Disk device key
        iops_limit: IOPS limit value
        dry_run: If True, only preview without making changes
    
    Returns:
        dict: {success, message}
    """
    try:
        # Find the disk device
        disk_device = None
        for device in vm.config.hardware.device:
            if isinstance(device, vim.vm.device.VirtualDisk) and device.key == device_key:
                disk_device = device
                break
        
        if not disk_device:
            return {'success': False, 'message': f'Disk with key {device_key} not found'}
        
        if dry_run:
            return {
                'success': True,
                'message': f'[DRY RUN] Would set {disk_device.deviceInfo.label} IOPS to {iops_limit}',
                'dry_run': True
            }
        
        # Create storage I/O allocation spec
        storage_io_alloc = vim.vm.device.VirtualDisk.StorageIOAllocationInfo()
        storage_io_alloc.limit = iops_limit
        
        # Create device spec
        disk_device.storageIOAllocation = storage_io_alloc
        
        # Create VM config spec
        spec = vim.vm.ConfigSpec()
        device_spec = vim.vm.device.VirtualDeviceSpec()
        device_spec.operation = vim.vm.device.VirtualDeviceSpec.Operation.edit
        device_spec.device = disk_device
        
        spec.deviceChange = [device_spec]
        
        # Apply configuration
        task = vm.Reconfigure(spec)
        
        # Wait for task completion
        timeout = 60
        start_time = time.time()
        while time.time() - start_time < timeout:
            if task.info.state == vim.TaskInfo.State.success:
                return {
                    'success': True,
                    'message': f'Successfully set {disk_device.deviceInfo.label} IOPS to {iops_limit}'
                }
            elif task.info.state == vim.TaskInfo.State.error:
                return {
                    'success': False,
                    'message': f'Failed to set IOPS: {task.info.error.msg}'
                }
            time.sleep(1)
        
        return {'success': False, 'message': 'Task timeout'}
    
    except Exception as e:
        return {'success': False, 'message': f'Error setting IOPS: {str(e)}'}

def list_vps(args):
    """List VPS instances with details"""
    print("=" * 140)
    print("📋 VPS INSTANCES LIST")
    print("=" * 140)
    
    vps_list = get_vps_list(ignore_name=args.ignore_name, ignore_email=args.ignore_email)
    
    if not vps_list:
        print("❌ No VPS instances found")
        return
    
    print(f"\n🔍 Found {len(vps_list)} VPS instance(s)")
    if args.ignore_name:
        print(f"   (Ignoring names containing: '{args.ignore_name}')")
    if args.ignore_email:
        print(f"   (Ignoring emails containing: '{args.ignore_email}')")
    
    # Connect to vCenter to get IOPS info
    vcenter_host = os.getenv('VCENTER_DOMAIN')
    vcenter_user = os.getenv('VCENTER_UID')
    vcenter_password = os.getenv('VCENTER_PW')
    
    si = None
    content = None
    if vcenter_host and vcenter_user and vcenter_password:
        try:
            context = ssl._create_unverified_context()
            si = SmartConnect(
                host=vcenter_host,
                user=vcenter_user,
                pwd=vcenter_password,
                sslContext=context
            )
            if si:
                content = si.RetrieveContent()
                print("✅ Connected to vCenter\n")
        except:
            print("⚠️  Could not connect to vCenter (IOPS info will be 'N/A')\n")
    
    print("-" * 140)
    print(f"{'ID':<6} {'Name':<25} {'User Email':<30} {'IOPS':<12} {'CPU':<5} {'RAM':<8} {'Disk':<8} {'BIOS UUID':<40}")
    print("-" * 140)
    
    for vps in vps_list:
        iops_str = "N/A"
        
        # Get IOPS from vCenter if available
        if content and vps['bios_uuid']:
            try:
                vm = get_obj(content, [vim.VirtualMachine], uuid=vps['bios_uuid'])
                if vm:
                    disks = get_vm_disks_info(vm)
                    if disks:
                        # Aggregate IOPS from all disks
                        iops_values = [d['iops_limit'] for d in disks if d['iops_limit']]
                        if iops_values:
                            iops_str = f"{sum(iops_values)}"
                        else:
                            iops_str = "unlimited"
            except:
                iops_str = "?"
        
        print(f"{vps['id']:<6} {(vps['name'][:22] + '...' if len(vps['name'] or '') > 25 else vps['name'] or 'N/A'):<25} "
              f"{(vps['email'][:27] + '...' if len(vps['email'] or '') > 30 else vps['email'] or 'N/A'):<30} "
              f"{iops_str:<12} {vps['cpu']:<5} {str(vps['ram_gb'])+'GB':<8} {str(vps['disk_gb'])+'GB':<8} {(vps['bios_uuid'][:36] if vps['bios_uuid'] else 'N/A'):<40}")
    
    print("-" * 140)
    print(f"\n✅ Total: {len(vps_list)} VPS instance(s)")
    
    if si:
        Disconnect(si)
    
    print("=" * 140)

def set_iops_batch(args):
    """Set IOPS for VPS instances"""
    print("=" * 120)
    print("⚙️  VPS BATCH IOPS CONFIGURATION")
    print("=" * 120)
    
    vps_list = get_vps_list(ignore_name=args.ignore_name, ignore_email=args.ignore_email)
    
    if not vps_list:
        print("❌ No VPS instances found")
        return
    
    print(f"\n🔍 Found {len(vps_list)} VPS instance(s)")
    print(f"📊 Setting IOPS to: {args.set_iops}")
    
    if args.dry_run:
        print("⚠️  DRY RUN MODE - no changes will be made")
    
    if args.ignore_name:
        print(f"   (Ignoring names containing: '{args.ignore_name}')")
    if args.ignore_email:
        print(f"   (Ignoring emails containing: '{args.ignore_email}')")
    
    # Connect to vCenter
    vcenter_host = os.getenv('VCENTER_DOMAIN')
    vcenter_user = os.getenv('VCENTER_UID')
    vcenter_password = os.getenv('VCENTER_PW')
    
    if not vcenter_host or not vcenter_user or not vcenter_password:
        print("❌ Missing vCenter credentials in .env")
        return
    
    try:
        context = ssl._create_unverified_context()
        print(f"\n🔗 Connecting to vCenter: {vcenter_host}")
        si = SmartConnect(
            host=vcenter_host,
            user=vcenter_user,
            pwd=vcenter_password,
            sslContext=context
        )
        
        if not si:
            print("❌ Failed to connect to vCenter")
            return
        
        print("✅ Connected to vCenter\n")
        atexit.register(Disconnect, si)
        
        content = si.RetrieveContent()
        
        # Process each VPS
        success_count = 0
        error_count = 0
        skip_count = 0
        
        for idx, vps in enumerate(vps_list, 1):
            vps_id = vps['id']
            vps_name = vps['name']
            bios_uuid = vps['bios_uuid']
            user_email = vps['email'] or 'N/A'
            
            print(f"\n[{idx}/{len(vps_list)}] VPS #{vps_id}: {vps_name} (User: {user_email})")
            
            # Check if bios_uuid is available
            if not bios_uuid:
                print(f"  ⚠️  Skipping - No bios_uuid (VM not created yet?)")
                skip_count += 1
                continue
            
            # Find VM in vCenter by UUID
            print(f"  🔍 Finding VM by UUID: {bios_uuid[:8]}...")
            vm = get_obj(content, [vim.VirtualMachine], uuid=bios_uuid)
            
            if not vm:
                print(f"  ❌ VM not found in vCenter (UUID: {bios_uuid})")
                error_count += 1
                continue
            
            print(f"  ✅ Found VM: {vm.name}")
            
            # Get disk information
            disks = get_vm_disks_info(vm)
            
            if not disks:
                print(f"  ⚠️  No disks found")
                skip_count += 1
                continue
            
            print(f"  📦 Found {len(disks)} disk(s):")
            
            # Set IOPS for each disk
            disk_success = 0
            disk_error = 0
            
            for disk in disks:
                device_key = disk['device_key']
                disk_name = disk['device_name']
                capacity_gb = disk['capacity_gb']
                current_iops = disk['iops_limit']
                
                print(f"     • {disk_name} ({capacity_gb:.1f}GB, current IOPS: {current_iops or 'unlimited'})")
                
                result = set_disk_iops(vm, device_key, args.set_iops, dry_run=args.dry_run)
                
                if result['success']:
                    print(f"       ✅ {result['message']}")
                    disk_success += 1
                else:
                    print(f"       ❌ {result['message']}")
                    disk_error += 1
            
            if disk_error == 0:
                success_count += 1
            else:
                error_count += 1
        
        Disconnect(si)
        
        # Summary
        print("\n" + "=" * 120)
        print("📊 SUMMARY")
        print("=" * 120)
        print(f"Total VPS processed: {len(vps_list)}")
        print(f"  ✅ Success: {success_count}")
        print(f"  ❌ Errors: {error_count}")
        print(f"  ⊘  Skipped: {skip_count}")
        
        if args.dry_run:
            print("\n⚠️  DRY RUN MODE - No changes were made to vCenter")
        
        print("=" * 120)
    
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()

def main():
    parser = argparse.ArgumentParser(
        description='VPS Batch IOPS Configuration',
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Examples:
  python 03-set-iops-batch.py --list
  python 03-set-iops-batch.py --list --ignore-name "test"
  python 03-set-iops-batch.py --list --ignore-email "gmail"
  python 03-set-iops-batch.py --set-iops 1000
  python 03-set-iops-batch.py --set-iops 1000 --ignore-name "test" --dry-run
        """
    )
    
    parser.add_argument('--list', action='store_true', help='List VPS instances')
    parser.add_argument('--set-iops', type=int, help='Set IOPS limit for all disks')
    parser.add_argument('--ignore-name', type=str, help='Ignore VPS names containing this string')
    parser.add_argument('--ignore-email', type=str, help='Ignore user emails containing this string')
    parser.add_argument('--dry-run', action='store_true', help='Preview without making changes')
    
    args = parser.parse_args()
    
    # Validate arguments
    if not args.list and not args.set_iops:
        parser.print_help()
        sys.exit(1)
    
    if args.list:
        list_vps(args)
    
    if args.set_iops:
        set_iops_batch(args)

if __name__ == "__main__":
    main()
