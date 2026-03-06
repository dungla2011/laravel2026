#!/usr/bin/env python3
"""
VPS Creation Daemon
Continuously monitors vps_instances table and creates VPS instances based on init_os
Clones VMs and updates creation status in database

Install: pip install pyvmomi pymysql python-dotenv
"""

import atexit
import ssl
import sys
import os
import json
import time
import threading
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

def wait_for_task(task, timeout=3600, progress_callback=None):
    """Wait for vSphere task to complete"""
    start_time = time.time()
    last_update = start_time

    while time.time() - start_time < timeout:
        if task.info.state == vim.TaskInfo.State.success:
            elapsed = time.time() - start_time
            print(f"✅ Task completed successfully in {elapsed:.2f}s")
            return True, elapsed
        elif task.info.state == vim.TaskInfo.State.error:
            elapsed = time.time() - start_time
            print(f"❌ Task failed: {task.info.error.msg}")
            return False, elapsed

        # Print progress every 10 seconds
        now = time.time()
        if now - last_update >= 10:
            elapsed = now - start_time
            progress = getattr(task.info, 'progress', 0) or 0
            msg = f"⏳ Task running... ({elapsed:.1f}s, progress: {progress}%)"
            print(msg)
            if progress_callback:
                progress_callback(msg, progress)
            last_update = now

        time.sleep(1)

    print(f"❌ Task timeout after {timeout}s")
    return False, timeout

def clone_vm(source_vm_name, dest_vm_name, vcenter_host, vcenter_user, vcenter_password, progress_callback=None):
    """
    Clone a VM and return result dict

    Args:
        progress_callback: Optional function to call with progress updates (msg, percent)

    Returns:
        dict with success, new_vm_id, error, etc.
    """
    result = {
        'success': False,
        'source_vm_name': source_vm_name,
        'dest_vm': dest_vm_name,
        'dest_vm_id': None,
        'new_vm_id': None,
        'error': '',
        'message': ''
    }

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
            result['error'] = f"Failed to connect to vCenter: {vcenter_host}"
            print(f"❌ {result['error']}")
            return result

        print(f"✅ Connected to vCenter")
        atexit.register(Disconnect, si)

        content = si.RetrieveContent()

        # Find source VM
        print(f"\n📋 Finding source VM: {source_vm_name}")
        source_vm = get_obj(content, [vim.VirtualMachine], name=source_vm_name)

        if not source_vm:
            result['error'] = f"Source VM '{source_vm_name}' not found"
            print(f"❌ {result['error']}")
            return result

        print(f"✅ Found source VM")
        print(f"   - Name: {source_vm.name}")
        print(f"   - CPU: {source_vm.config.hardware.numCPU}")
        print(f"   - Memory: {source_vm.config.hardware.memoryMB}MB")

        # Prepare clone spec
        rel_spec = vim.vm.RelocateSpec()

        # Use default resource pool
        resource_pools = content.viewManager.CreateContainerView(
            content.rootFolder,
            [vim.ResourcePool],
            True
        )
        if resource_pools.view:
            rel_spec.pool = resource_pools.view[0]
            print(f"✓ Using resource pool: {resource_pools.view[0].name}")
        resource_pools.Destroy()

        # Use source VM's datastore
        if source_vm.datastore:
            rel_spec.datastore = source_vm.datastore[0]
            print(f"✓ Using datastore: {source_vm.datastore[0].name}")

        clone_spec = vim.vm.CloneSpec()
        clone_spec.location = rel_spec
        clone_spec.powerOn = False
        clone_spec.template = False

        # Execute clone
        print(f"\n🚀 Starting clone: {source_vm.name} → {dest_vm_name}")
        task = source_vm.Clone(
            folder=source_vm.parent,
            name=dest_vm_name,
            spec=clone_spec
        )

        # Wait for clone
        print(f"⏳ Waiting for clone operation to complete...")
        success, elapsed = wait_for_task(task, timeout=3600, progress_callback=progress_callback)

        if success:
            print(f"\n✅ Clone completed in {elapsed:.2f}s")

            # Find the new VM
            print(f"🔍 Finding cloned VM: {dest_vm_name}")
            time.sleep(2)

            new_vm = None
            for attempt in range(5):
                new_vm = get_obj(content, [vim.VirtualMachine], name=dest_vm_name)
                if new_vm:
                    break
                print(f"  ⏳ Attempt {attempt+1}/5...")
                time.sleep(2)

            if new_vm and new_vm.config:
                result['new_vm_id'] = new_vm.config.uuid
                result['dest_vm_id'] = new_vm.config.uuid
                result['success'] = True
                result['message'] = f"VM cloned successfully: {dest_vm_name}"
                print(f"✅ Found cloned VM: {new_vm.name}")
                print(f"   - ID: {new_vm.config.uuid}")
            else:
                result['error'] = "Could not find cloned VM UUID"
                print(f"⚠️  {result['error']}")
        else:
            result['error'] = "Clone operation failed or timed out"
            print(f"❌ {result['error']}")

        Disconnect(si)

    except vmodl.MethodFault as e:
        result['error'] = f"vSphere API error: {e.msg}"
        print(f"❌ {result['error']}")
    except Exception as e:
        result['error'] = str(e)
        print(f"❌ Error: {result['error']}")

    return result

def resize_vm_hardware(vm, cpu, ram_gb, disk_gb, disk_gb_current):
    """
    Resize VM hardware: CPU, RAM, Disk
    - Không cần bật VM (VM có thể tắt)
    - Disk chỉ extend nếu disk_gb > disk_gb_current
    """
    try:
        print(f"\n⚙️  Resizing hardware...")

        spec = vim.vm.ConfigSpec()
        device_change_list = []

        # 1. Resize CPU
        if cpu and cpu > 0:
            print(f"  💻 CPU: {cpu}")
            spec.numCPUs = int(cpu)

        # 2. Resize RAM
        if ram_gb and ram_gb > 0:
            print(f"  🧠 RAM: {ram_gb}GB")
            spec.memoryMB = int(ram_gb * 1024)

        # 3. Extend Disk (chỉ nếu lớn hơn cái cũ)
        if disk_gb and disk_gb > 0:
            disk_gb = float(disk_gb)
            disk_gb_current = float(disk_gb_current)

            if disk_gb > disk_gb_current:
                print(f"  💾 Disk: {disk_gb_current:.1f}GB → {disk_gb:.1f}GB")

                # Tìm main disk (thường là disk đầu tiên)
                for device in vm.config.hardware.device:
                    if isinstance(device, vim.vm.device.VirtualDisk):
                        # Tính kích thước theo KB
                        new_size_kb = int(disk_gb * 1024 * 1024)  # GB -> KB
                        device.capacityInKB = new_size_kb

                        # Tạo device change spec
                        device_spec = vim.vm.device.VirtualDeviceSpec()
                        device_spec.operation = vim.vm.device.VirtualDeviceSpec.Operation.edit
                        device_spec.device = device

                        device_change_list.append(device_spec)
                        break  # Chỉ extend disk đầu tiên
            else:
                print(f"  ⚠️  Disk: {disk_gb}GB ≤ {disk_gb_current:.1f}GB (không resize)")

        spec.deviceChange = device_change_list

        # Apply configuration
        print(f"  ⏳ Applying configuration...")
        task = vm.Reconfigure(spec)

        # Wait for task
        start_time = time.time()
        timeout = 300
        while time.time() - start_time < timeout:
            if task.info.state == vim.TaskInfo.State.success:
                elapsed = time.time() - start_time
                print(f"  ✅ Hardware resize completed in {elapsed:.2f}s")
                return True
            elif task.info.state == vim.TaskInfo.State.error:
                print(f"  ❌ Resize failed: {task.info.error.msg}")
                return False
            time.sleep(1)

        print(f"  ❌ Resize timeout")
        return False

    except Exception as e:
        print(f"  ❌ Error: {e}")
        return False

def update_vps_status(vps_id, create_status, create_vps_progress=None):
    """Update VPS creation status in database"""
    with db_lock:
        conn = get_db_connection()
        if not conn:
            print(f"❌ Failed to connect to database for VPS #{vps_id}")
            return False

        try:
            with conn.cursor() as cursor:
                if create_vps_progress:
                    sql = """
                    UPDATE vps_instances
                    SET create_status = %s, create_vps_progress = %s
                    WHERE id = %s
                    """
                    cursor.execute(sql, (create_status, create_vps_progress, vps_id))
                else:
                    sql = """
                    UPDATE vps_instances
                    SET create_status = %s
                    WHERE id = %s
                    """
                    cursor.execute(sql, (create_status, vps_id))

            conn.commit()
            print(f"  ✅ VPS #{vps_id} status: {create_status}")
            return True
        except Exception as e:
            print(f"  ❌ VPS #{vps_id} status update failed: {e}")
            return False
        finally:
            conn.close()

def create_vps_thread(vps_id, init_os_id, vps_name):
    """Thread function to create a VPS"""
    print(f"\n{'='*60}")
    print(f"🔧 Creating VPS #{vps_id}")
    print(f"{'='*60}")

    progress = {
        'vps_id': vps_id,
        'start_time': datetime.now().isoformat(),
        'steps': []
    }

    # Get OS info from database
    with db_lock:
        conn = get_db_connection()
        if not conn:
            print(f"❌ VPS #{vps_id}: Failed to connect to database")
            update_vps_status(vps_id, 'vps_create_error', json.dumps(progress))
            return

        try:
            with conn.cursor() as cursor:
                sql = "SELECT id, vm_name FROM vps_os_versions WHERE id = %s AND is_active = 1"
                cursor.execute(sql, (init_os_id,))
                os_info = cursor.fetchone()

            if not os_info or not os_info.get('vm_name'):
                print(f"❌ VPS #{vps_id}: OS version {init_os_id} not found")
                progress['steps'].append({
                    'stage': 'error',
                    'timestamp': datetime.now().isoformat(),
                    'status': f'OS version {init_os_id} not found or no vm_name'
                })
                update_vps_status(vps_id, 'vps_create_error', json.dumps(progress))
                return

            source_vm_name = os_info['vm_name']
            print(f"📋 VPS #{vps_id}: Source VM: {source_vm_name}")

        except Exception as e:
            print(f"❌ VPS #{vps_id}: Database error: {str(e)}")
            progress['steps'].append({
                'stage': 'error',
                'timestamp': datetime.now().isoformat(),
                'status': f'Database error: {str(e)}'
            })
            update_vps_status(vps_id, 'vps_create_error', json.dumps(progress))
            return
        finally:
            conn.close()

    # Mark as creating
    print(f"🔄 VPS #{vps_id}: Updating status to vps_creating")
    progress['steps'].append({
        'stage': 'status_change',
        'timestamp': datetime.now().isoformat(),
        'status': 'Updating status to vps_creating'
    })

    # Save initial unified progress format
    initial_progress = {
        'vps_id': vps_id,
        'start_time': progress['start_time'],
        'end_time': None,
        'current_stage': 'preparing',
        'current_status': 'Preparing to clone VM',
        'percent': 0,
        'success': None,
        'steps': progress['steps']
    }
    update_vps_status(vps_id, 'vps_creating', json.dumps(initial_progress))

    # Use VPS name from database as destination VM name
    dest_vm_name = vps_name
    print(f"🎯 VPS #{vps_id}: Clone destination VM name: {dest_vm_name}")

    # Clone the VM
    print(f"🚀 VPS #{vps_id}: Starting clone operation")
    progress['steps'].append({
        'stage': 'cloning',
        'timestamp': datetime.now().isoformat(),
        'status': f'Starting clone from {source_vm_name}'
    })
    update_vps_status(vps_id, 'vps_creating', json.dumps(progress))

    vcenter_host = os.getenv('VCENTER_DOMAIN')
    vcenter_user = os.getenv('VCENTER_UID')
    vcenter_password = os.getenv('VCENTER_PW')

    # Define progress callback for clone operation
    def clone_progress_callback(msg, percent):
        """Callback to track clone progress and save to database"""
        print(f"  📝 Saving progress to DB: {msg}")
        current_progress = {
            'vps_id': vps_id,
            'start_time': progress['start_time'],
            'end_time': None,
            'current_stage': 'cloning',
            'current_status': msg,
            'percent': percent,
            'success': None,
            'steps': progress['steps']
        }
        result = update_vps_status(vps_id, 'vps_creating', json.dumps(current_progress))
        if result:
            print(f"  ✅ Progress saved to vps_instances.create_vps_progress")
        else:
            print(f"  ❌ Failed to save progress to database")

    clone_result = clone_vm(source_vm_name, dest_vm_name, vcenter_host, vcenter_user, vcenter_password, progress_callback=clone_progress_callback)

    # Add clone completion to progress steps
    progress['steps'].append({
        'stage': 'clone_complete',
        'timestamp': datetime.now().isoformat(),
        'status': 'Clone completed',
        'success': clone_result['success'],
        'new_vm_id': clone_result['new_vm_id']
    })

    if clone_result['success']:
        print(f"✅ Clone successful!")
        print(f"   New VM ID: {clone_result['new_vm_id']}")

        # Get hardware info from database
        vps_hw = None
        with db_lock:
            conn = get_db_connection()
            if conn:
                try:
                    with conn.cursor() as cursor:
                        sql = "SELECT cpu, ram_gb, disk_gb FROM vps_instances WHERE id = %s AND deleted_at IS NULL"
                        cursor.execute(sql, (vps_id,))
                        vps_hw = cursor.fetchone()
                finally:
                    conn.close()

        # Connect to vCenter for hardware resize and power on
        mac_addresses = []
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
                vm = get_obj(content, [vim.VirtualMachine], name=dest_vm_name)

                if vm:
                    # Get MAC addresses from VM
                    print(f"\n📝 Getting MAC addresses...")
                    for device in vm.config.hardware.device:
                        if isinstance(device, vim.vm.device.VirtualEthernetCard):
                            if hasattr(device, 'macAddress') and device.macAddress:
                                mac_addresses.append(device.macAddress.lower())
                                print(f"  ✅ Found MAC: {device.macAddress.lower()}")

                    # Resize hardware if needed
                    if vps_hw and (vps_hw.get('cpu') or vps_hw.get('ram_gb') or vps_hw.get('disk_gb')):
                        print(f"\n⚙️  Configuring hardware resources...")

                        # Get current disk size from template
                        disk_gb_current = 0
                        for device in vm.config.hardware.device:
                            if isinstance(device, vim.vm.device.VirtualDisk):
                                disk_gb_current = device.capacityInKB / (1024 * 1024)
                                break

                        # Resize
                        resize_result = resize_vm_hardware(
                            vm,
                            vps_hw.get('cpu'),
                            vps_hw.get('ram_gb'),
                            vps_hw.get('disk_gb'),
                            disk_gb_current
                        )

                        if resize_result:
                            progress['steps'].append({
                                'stage': 'hardware_config',
                                'timestamp': datetime.now().isoformat(),
                                'status': f'Hardware configured: {vps_hw.get("cpu", "N/A")}C {vps_hw.get("ram_gb", "N/A")}GB {vps_hw.get("disk_gb", "N/A")}GB'
                            })
                        else:
                            progress['steps'].append({
                                'stage': 'warning',
                                'timestamp': datetime.now().isoformat(),
                                'status': 'Hardware resize failed, continuing...'
                            })

                    # Power on VM
                    print(f"\n🔌 Powering on VM...")
                    if vm.runtime.powerState != vim.VirtualMachine.PowerState.poweredOn:
                        task = vm.PowerOn()
                        time.sleep(3)  # Wait for power on task to start
                        print(f"  ✅ VM powered on")

                        # Update power_state in database
                        with db_lock:
                            conn = get_db_connection()
                            if conn:
                                try:
                                    with conn.cursor() as cursor:
                                        sql = "UPDATE vps_instances SET power_state = %s WHERE id = %s"
                                        cursor.execute(sql, ('POWERED_ON', vps_id))
                                    conn.commit()
                                    print(f"  💾 Updated power_state to POWERED_ON")
                                except Exception as e:
                                    print(f"  ⚠️  Failed to update power_state: {e}")
                                finally:
                                    conn.close()

                        progress['steps'].append({
                            'stage': 'power_on',
                            'timestamp': datetime.now().isoformat(),
                            'status': 'VM powered on'
                        })
                    else:
                        print(f"  ℹ️  VM already powered on")

                Disconnect(si)
        except Exception as e:
            print(f"⚠️  Error during hardware config/power on: {e}")
            progress['steps'].append({
                'stage': 'warning',
                'timestamp': datetime.now().isoformat(),
                'status': f'Hardware/power warning: {str(e)}'
            })

        # Final progress with all steps
        final_progress = {
            'vps_id': vps_id,
            'start_time': progress['start_time'],
            'end_time': datetime.now().isoformat(),
            'current_stage': 'clone_complete',
            'current_status': f'Clone completed successfully: {dest_vm_name}',
            'percent': 100,
            'success': True,
            'steps': progress['steps']
        }

        # Update VPS with new VM ID (do not change name)
        with db_lock:
            conn = get_db_connection()
            if conn:
                try:
                    with conn.cursor() as cursor:
                        mac_address_str = ','.join(mac_addresses) if mac_addresses else None
                        if mac_address_str:
                            print(f"  💾 Saving MAC addresses to DB: {mac_address_str}")

                        sql = """
                        UPDATE vps_instances
                        SET create_status = %s,
                            vmware_vm_id = %s,
                            bios_uuid = %s,
                            init_mac_address = %s,
                            create_vps_progress = %s
                        WHERE id = %s
                        """
                        cursor.execute(sql, (
                            'vps_create_done',
                            clone_result['new_vm_id'],
                            clone_result['new_vm_id'],
                            mac_address_str,
                            json.dumps(final_progress),
                            vps_id
                        ))
                    conn.commit()
                    print(f"✅ VPS #{vps_id} creation completed!")
                except Exception as e:
                    print(f"❌ Error updating VPS: {e}")
                finally:
                    conn.close()
    else:
        print(f"❌ Clone failed: {clone_result['error']}")
        # Save error progress with all steps
        error_progress = {
            'vps_id': vps_id,
            'start_time': progress['start_time'],
            'end_time': datetime.now().isoformat(),
            'current_stage': 'clone_error',
            'current_status': clone_result['error'],
            'percent': 0,
            'success': False,
            'steps': progress['steps']
        }
        update_vps_status(vps_id, 'vps_create_error', json.dumps(error_progress))

def get_pending_vps_creations():
    """Get VPS instances with create_status = 'vps_new_create'"""
    conn = get_db_connection()
    if not conn:
        return []

    try:
        with conn.cursor() as cursor:
            sql = "SELECT * FROM vps_instances WHERE create_status = %s AND deleted_at IS NULL"
            cursor.execute(sql, ('vps_new_create',))
            return cursor.fetchall()
    except Exception as e:
        print(f"❌ Error querying VPS list: {e}")
        return []
    finally:
        conn.close()

def main():
    """Main loop - continuously check for VPS creation requests"""
    print("=" * 60)
    print("🚀 VPS Creation Daemon Started")
    print("=" * 60)
    print(f"Database: {os.getenv('DB_HOST')}:{os.getenv('DB_PORT')}/{os.getenv('DB_DATABASE')}")
    print(f"vCenter: {os.getenv('VCENTER_DOMAIN')}")
    print(f"Loop interval: 5 seconds")
    print("=" * 60)

    threads = []

    try:
        while True:
            # Get pending VPS creations
            pending_vps = get_pending_vps_creations()

            if pending_vps:
                print(f"\n📊 Found {len(pending_vps)} VPS(s) pending creation")

                for vps in pending_vps:
                    vps_id = vps['id']
                    init_os = vps['init_os']
                    vps_name = vps['name']

                    print(f"  → VPS #{vps_id}: init_os={init_os}, name={vps_name}")

                    # Start creation thread
                    thread = Thread(
                        target=create_vps_thread,
                        args=(vps_id, init_os, vps_name),
                        daemon=True
                    )
                    threads.append(thread)
                    thread.start()

            # Clean up finished threads
            threads = [t for t in threads if t.is_alive()]

            # Wait 5 seconds before next check
            print(f"\n⏱️  Waiting 5 seconds for next check... ({len(threads)} thread(s) running)")
            time.sleep(5)

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
