<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncVmwareInstancesCommand extends Command
{
    protected $signature = 'vmware:sync-instances {--domain=} {--uid=} {--pw=} {--power-state=POWERED_ON}';
    protected $description = 'Sync VMware instances from vCenter to vps_instances and vps_usages tables';

    public function handle()
    {
        // Get credentials from options or env
        $domain = $this->option('domain') ?? env('VCENTER_DOMAIN');
        $uid = $this->option('uid') ?? env('VCENTER_UID');
        $pw = $this->option('pw') ?? env('VCENTER_PW');
        $powerState = $this->option('power-state');

        if (!$domain || !$uid || !$pw) {
            $this->error('❌ vCenter credentials required. Provide via --domain, --uid, --pw or env variables');
            return 1;
        }

        $this->info("🔗 Connecting to vCenter: $domain");

        // Include VMware helper from public/tool1/22.php
        require_once base_path('public/tool1/22.php');

        // Login to vCenter
        if (!VmwareHelper::loginVC($domain, $uid, $pw)) {
            $this->error('❌ Failed to connect to vCenter');
            return 1;
        }

        $this->info('✅ Connected to vCenter');

        try {
            // Get all VMs
            $this->info("\n📋 Fetching VM list from vCenter...");
            $vms = VmwareHelper::getVMList($powerState);

            if (empty($vms)) {
                $this->warn('⚠️  No VMs found');
                return 0;
            }

            $this->info("✅ Found " . count($vms) . " VMs");
            $this->info("\n📝 Processing VMs...\n");

            $synced = 0;
            $updated = 0;
            $failed = 0;

            foreach ($vms as $vm) {
                try {
                    $this->line("Processing: {$vm->name} ({$vm->vm})");

                    // Get detailed VM info
                    $vmInfo = VmwareHelper::getVMInfo($vm->vm);
                    
                    if (!$vmInfo) {
                        $this->warn("  ⚠️  Failed to get details for {$vm->name}");
                        $failed++;
                        continue;
                    }

                    // Prepare data for vps_instances
                    $instanceData = [
                        'name' => $vm->name,
                        'vmware_vm_id' => $vm->vm,
                        'bios_uuid' => $vmInfo->identity->bios_uuid,
                        'instance_uuid' => $vmInfo->identity->instance_uuid,
                        'cpu' => $vmInfo->cpu->count,
                        'ram_gb' => intval($vmInfo->memory->size_MiB / 1024),
                        'disk_gb' => intval($this->getTotalDiskGB($vmInfo)),
                        'number_ip_address' => count($vmInfo->nics),
                        'power_state' => $vmInfo->power_state,
                        'status' => ($vmInfo->power_state === 'POWERED_ON') ? 1 : 0,
                        'full_info' => json_encode($vmInfo),
                        'updated_at' => now(),
                    ];

                    // Upsert to vps_instances (by bios_uuid or vmware_vm_id)
                    $instance = DB::table('vps_instances')
                        ->where('vmware_vm_id', $vm->vm)
                        ->first();

                    if ($instance) {
                        DB::table('vps_instances')
                            ->where('id', $instance->id)
                            ->update($instanceData);
                        $this->line("  ✏️  Updated vps_instances (ID: {$instance->id})");
                        $updated++;
                    } else {
                        $instanceData['created_at'] = now();
                        $instanceId = DB::table('vps_instances')->insertGetId($instanceData);
                        $this->line("  ✨ Created vps_instances (ID: $instanceId)");
                        $synced++;
                    }

                    // Get instance ID for vps_usages
                    $instance = DB::table('vps_instances')
                        ->where('vmware_vm_id', $vm->vm)
                        ->first();

                    if ($instance) {
                        // Insert into vps_usages (usage snapshot at this moment)
                        DB::table('vps_usages')->insert([
                            'name' => $vm->name,
                            'instance_id' => $instance->id,
                            'user_id' => $instance->user_id,
                            'timestamp_minute' => now()->startOfMinute(),
                            'number_ip_address' => count($vmInfo->nics),
                            'power_state' => $vmInfo->power_state,
                            'bios_uuid' => $vmInfo->identity->bios_uuid,
                            'instance_uuid' => $vmInfo->identity->instance_uuid,
                            'full_info' => json_encode($vmInfo),
                            'price_per_minute' => $instance->price_per_minute ?? 0,
                            'status' => 1,
                            'created_at' => now(),
                        ]);
                        $this->line("  📊 Created vps_usages snapshot");
                    }

                } catch (\Exception $e) {
                    $this->error("  ❌ Error processing {$vm->name}: " . $e->getMessage());
                    $failed++;
                    continue;
                }
            }

            // Summary
            $this->line("\n" . str_repeat('=', 60));
            $this->info("✅ Sync Complete");
            $this->line("  Created: $synced");
            $this->line("  Updated: $updated");
            $this->line("  Failed: $failed");
            $this->line("  Total: " . count($vms));
            $this->line(str_repeat('=', 60));

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Sync failed: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Get total disk size in GB from VmHardwareInfo
     */
    private function getTotalDiskGB($vmInfo): float
    {
        if (method_exists($vmInfo, 'getTotalDiskGB')) {
            return $vmInfo->getTotalDiskGB();
        }

        $total = 0;
        if (isset($vmInfo->disks) && is_array($vmInfo->disks)) {
            foreach ($vmInfo->disks as $disk) {
                if (isset($disk->capacity)) {
                    $total += $disk->capacity;
                }
            }
        }
        return round($total / (1024 * 1024 * 1024), 2);
    }
}
