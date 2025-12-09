<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\Vmware\VmwareHelper;

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
                        // 'number_ip_address' => count($vmInfo->nics),
                        'power_state' => $vmInfo->power_state,
                        'status' => ($vmInfo->power_state === 'POWERED_ON') ? 1 : 0,
                        'price_per_minute' => 0, // Default price, can be updated later
                        'full_info' => json_encode($vmInfo),
                        'updated_at' => now(),
                    ];

                    // Upsert to vps_instances (by bios_uuid - unique identifier)
                    $instance = DB::table('vps_instances')
                        ->where('bios_uuid', $vmInfo->identity->bios_uuid)
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
                        ->where('bios_uuid', $vmInfo->identity->bios_uuid)
                        ->first();

                    if ($instance) {
                        // Get latest vps_usages record for this bios_uuid
                        $lastUsage = DB::table('vps_usages')
                            ->where('bios_uuid', $vmInfo->identity->bios_uuid)
                            ->orderBy('id', 'desc')
                            ->first();

                        // Get IP addresses (placeholder - to be populated by getIpFromMacAddress())
                        $listIpAddress = ''; // Will be populated by getIpFromMacAddress() method when available
                        
                        // Calculate config hash for comparison (including IP addresses)
                        $currentConfigHash = md5(json_encode([
                            'cpu' => $vmInfo->cpu->count,
                            'ram_gb' => intval($vmInfo->memory->size_MiB / 1024),
                            'disk_gb' => intval($this->getTotalDiskGB($vmInfo)),
                            'power_state' => $vmInfo->power_state,
                            'list_ip_address' => $listIpAddress,
                        ]));

                        if ($lastUsage) {
                            // Config hash from last record (including IP addresses)
                            $lastConfigHash = md5(json_encode([
                                'cpu' => $lastUsage->cpu,
                                'ram_gb' => $lastUsage->ram_gb,
                                'disk_gb' => $lastUsage->disk_gb,
                                'power_state' => $lastUsage->power_state,
                                'list_ip_address' => $lastUsage->list_ip_address ?? '',
                            ]));

                            $timeSinceSame = now()->diffInMinutes($lastUsage->lastest_time_the_same ?? $lastUsage->created_at);

                            // If config is the same AND time since last same is <= 10 minutes
                            if ($currentConfigHash === $lastConfigHash && $timeSinceSame <= 10) {
                                // Just update the record
                                DB::table('vps_usages')
                                    ->where('id', $lastUsage->id)
                                    ->update([
                                        'count_update_status' => DB::raw('count_update_status + 1'),
                                        'lastest_time_the_same' => now(),
                                        'timestamp_minute' => now()->startOfMinute(),
                                        'last_found_ip' => now(),
                                    ]);
                                $this->line("  ♻️  Updated vps_usages (count_update: " . ($lastUsage->count_update_status + 1) . ")");
                            } else {
                                // Config changed or 10+ minutes passed, insert new record
                                DB::table('vps_usages')->insert([
                                    'name' => $vm->name,
                                    'instance_id' => $instance->id,
                                    'vmware_vm_id' => $vm->vm,
                                    'cpu' => $vmInfo->cpu->count,
                                    'ram_gb' => intval($vmInfo->memory->size_MiB / 1024),
                                    'disk_gb' => intval($this->getTotalDiskGB($vmInfo)),
                                    'user_id' => $instance->user_id,
                                    'timestamp_minute' => now()->startOfMinute(),
                                    'power_state' => $vmInfo->power_state,
                                    'bios_uuid' => $vmInfo->identity->bios_uuid,
                                    'instance_uuid' => $vmInfo->identity->instance_uuid,
                                    'full_info' => json_encode($vmInfo),
                                    'price_per_minute' => $instance->price_per_minute ?? 0,
                                    'status' => 1,
                                    'count_update_status' => 0,
                                    'lastest_time_the_same' => now(),
                                    'list_ip_address' => $listIpAddress,
                                    'last_found_ip' => now(),
                                    'created_at' => now(),
                                ]);
                                $this->line("  📊 Inserted vps_usages snapshot (config changed or 10+ min passed)");
                            }
                        } else {
                            // No previous record, insert new one
                            DB::table('vps_usages')->insert([
                                'name' => $vm->name,
                                'instance_id' => $instance->id,
                                'vmware_vm_id' => $vm->vm,
                                'cpu' => $vmInfo->cpu->count,
                                'ram_gb' => intval($vmInfo->memory->size_MiB / 1024),
                                'disk_gb' => intval($this->getTotalDiskGB($vmInfo)),
                                'user_id' => $instance->user_id,
                                'timestamp_minute' => now()->startOfMinute(),
                                'power_state' => $vmInfo->power_state,
                                'bios_uuid' => $vmInfo->identity->bios_uuid,
                                'instance_uuid' => $vmInfo->identity->instance_uuid,
                                'full_info' => json_encode($vmInfo),
                                'price_per_minute' => $instance->price_per_minute ?? 0,
                                'status' => 1,
                                'count_update_status' => 0,
                                'lastest_time_the_same' => now(),
                                'list_ip_address' => $listIpAddress,
                                'last_found_ip' => now(),
                                'created_at' => now(),
                            ]);
                            $this->line("  📊 Inserted vps_usages snapshot (first record)");
                        }

                        // Check if config changed - only insert to history if it did
                        $configHash = md5(json_encode([
                            'cpu' => $vmInfo->cpu->count,
                            'ram_gb' => intval($vmInfo->memory->size_MiB / 1024),
                            'disk_gb' => intval($this->getTotalDiskGB($vmInfo)),
                            // 'number_ip_address' => count($vmInfo->nics),
                            'power_state' => $vmInfo->power_state,
                        ]));

                        $lastHistory = DB::table('vps_instance_config_histories')
                            ->where('instance_id', $instance->id)
                            ->orderBy('created_at', 'desc')
                            ->first();

                        $lastConfigHash = $lastHistory ? md5(json_encode([
                            'cpu' => $lastHistory->cpu,
                            'ram_gb' => $lastHistory->ram_gb,
                            'disk_gb' => $lastHistory->disk_gb,
                            // 'number_ip_address' => $lastHistory->number_ip_address,
                            'power_state' => $lastHistory->power_state,
                        ])) : null;

                        // Insert only if config changed or no history exists
                        if (!$lastHistory || $configHash !== $lastConfigHash) {
                            DB::table('vps_instance_config_histories')->insert([
                                'instance_id' => $instance->id,
                                'name' => $vm->name,
                                'vmware_vm_id' => $vm->vm,
                                'cpu' => $vmInfo->cpu->count,
                                'ram_gb' => intval($vmInfo->memory->size_MiB / 1024),
                                'disk_gb' => intval($this->getTotalDiskGB($vmInfo)),
                                // 'number_ip_address' => count($vmInfo->nics),
                                'power_state' => $vmInfo->power_state,
                                'bios_uuid' => $vmInfo->identity->bios_uuid,
                                'instance_uuid' => $vmInfo->identity->instance_uuid,
                                'full_info' => json_encode($vmInfo),
                                'price_per_minute' => $instance->price_per_minute ?? 0,
                                'created_at' => now(),
                            ]);
                            $this->line("  📝 Config changed, saved to history");
                        }
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
