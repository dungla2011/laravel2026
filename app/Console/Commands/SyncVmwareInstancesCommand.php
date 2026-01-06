<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\Vmware\VmwareHelper;
use App\Services\VpsPricingService;
use App\Services\VpsUsageFeeService;

class SyncVmwareInstancesCommand extends Command
{
    protected $signature = 'vmware:sync-instances {--domain=} {--uid=} {--pw=} '; //{--power-state=POWERED_ON}
    protected $description = 'Sync VMware instances from vCenter to vps_instances and vps_usages tables';

    public function handle()
    {
        // Get credentials from options or env
        $domain = $this->option('domain') ?? env('VCENTER_DOMAIN');
        $uid = $this->option('uid') ?? env('VCENTER_UID');
        $pw = $this->option('pw') ?? env('VCENTER_PW');
        // $powerState = $this->option('power-state') ?? null;
        $powerState = null;


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
            $vms = VmwareHelper::getVMListV2();

            

            if (empty($vms)) {
                $this->warn('⚠️  No VMs found');
                return 0;
            }

            $this->info("✅ Found " . count($vms) . " VMs");
            $this->info("\n📝 Processing VMs...\n");

            $synced = 0;
            $updated = 0;
            $failed = 0;

            $cc = 0;
            $tt = count($vms);
            foreach ($vms as $vm) {
                try {

                    $cc++;

                    // Normalize power_state to uppercase
                    $powerState = strtoupper($vm->power_state ?? 'poweredOff');
                    $powerState = str_replace('POWEREDON', 'POWERED_ON', $powerState);
                    $powerState = str_replace('POWEREDOFF', 'POWERED_OFF', $powerState);

                    $this->line("Processing: {$vm->name} ({$vm->vm_id} {$powerState})");
                    echo "\n $cc/$tt . Processing: $vm->name ($vm->vm_id $powerState)";

                    // Validate required fields
                    if (!$vm->bios_uuid) {
                        $this->warn("  ⚠️  No BIOS UUID for {$vm->name}, skipping");
                        $failed++;
                        continue;
                    }

                    // Prepare data for vps_instances
                    $instanceData = [
                        'name' => $vm->name,
                        'vmware_vm_id' => $vm->vm_id,
                        'bios_uuid' => $vm->bios_uuid,
                        'instance_uuid' => $vm->instance_uuid,
                        'cpu' => $vm->cpu,
                        'ram_gb' => intval($vm->memory_gb),
                        'disk_gb' => intval($vm->disk_gb),
                        'power_state' => $powerState,
                        'status' => ($powerState === 'POWERED_ON') ? 1 : 0,
                        'full_info' => json_encode($vm),
                        'updated_at' => now(),
                    ];

                    // Upsert to vps_instances (by bios_uuid - unique identifier)
                    $instance = DB::table('vps_instances')
                        ->where('bios_uuid', $vm->bios_uuid)
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
                        ->where('bios_uuid', $vm->bios_uuid)
                        ->first();

                    if ($instance) {
                        // Get latest vps_usages record for this bios_uuid
                        $lastUsage = DB::table('vps_usages')
                            ->where('bios_uuid', $vm->bios_uuid)
                            ->orderBy('id', 'desc')
                            ->first();



                        // Get IP addresses from MAC address mapping
                        $ipData = $this->getIpFromMacAddress($vm);
                        $listIpAddress = $ipData['list_ip_address'];
                        $lastFoundIpTime = $ipData['last_found_ip'];

                        // Calculate config hash for comparison (including IP addresses)
                        $currentConfigHash = md5(json_encode([
                            'cpu' => $vm->cpu,
                            'ram_gb' => intval($vm->memory_gb),
                            'disk_gb' => intval($vm->disk_gb),
                            'power_state' => $powerState,
                        ]));

                        if ($lastUsage) {

                            //Kiểm tra nếu MacAddress thay đổi thì update:
                            $currentMacAddresses = implode(',', $vm->mac_addresses ?? []);
                            if ($currentMacAddresses != ($lastUsage->mac_address ?? '')) {
                                // Update record with new MAC addresses
                                DB::table('vps_usages')
                                    ->where('id', $lastUsage->id)
                                    ->update([
                                        'mac_address' => $currentMacAddresses,
                                    ]);
                                $this->line("  🔄 MAC address changed, updated vps_usages record");
                            }

                            //Nếu tên khác thì cũng update tên:
                            if ($vm->name != ($lastUsage->name ?? '')) {
                                // Update record with new name
                                DB::table('vps_usages')
                                    ->where('id', $lastUsage->id)
                                    ->update([
                                        'name' => $vm->name,
                                    ]);
                                $this->line("  🔄 Name changed, updated vps_usages record");

                                //Update luôn trong vps_instances
                                DB::table('vps_instances')
                                    ->where('id', $instance->id)
                                    ->update([
                                        'name' => $vm->name,
                                    ]);

                            }


                            // Get current pricing config
                            $currentPricingConfig = VpsPricingService::getPricingConfig();

                            $currentPricingHash = md5(json_encode($currentPricingConfig));

                            // Check if pricing has changed from last record
                            $lastPricingConfig = $lastUsage->price_config
                                ? json_decode($lastUsage->price_config, true) : null;
//                                : VpsPricingService::getPricingConfig();
                            $lastPricingHash = md5(json_encode($lastPricingConfig));
                            $pricingHasChanged = $currentPricingHash !== $lastPricingHash;

//                            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                            print_r($lastPricingConfig);
//                            echo "</pre>";
//                            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                            print_r($currentPricingConfig);
//                            echo "</pre>";
//                            die("Change = $pricingHasChanged / $lastPricingHash");


                            // Config hash from last record (including IP addresses)
                            $lastConfigHash = md5(json_encode([
                                'cpu' => $lastUsage->cpu,
                                'ram_gb' => $lastUsage->ram_gb,
                                'disk_gb' => $lastUsage->disk_gb,
                                'power_state' => $lastUsage->power_state,
//                                'list_ip_address' => $lastUsage->list_ip_address ?? '',
                            ]));

                            $lastestTime = \Carbon\Carbon::parse($lastUsage->lastest_time_the_same ?? $lastUsage->created_at);
                            $createdTime = \Carbon\Carbon::parse($lastUsage->created_at);
                            
                            // Time since last update (for deciding to UPDATE or INSERT)
                            $timeSinceSame = $lastestTime->diffInMinutes(now());
                            
                            // Duration for fee calculation (from created_at to lastest_time_the_same)
                            $durationMinutes = $createdTime->diffInMinutes($lastestTime);

                            // If config is the same AND time since last same is <= 60 minutes AND pricing hasn't changed
                            if (($currentConfigHash === $lastConfigHash && $timeSinceSame <= 60 && !$pricingHasChanged)
                                || $instance->type == 'backup_glx' || $instance->type == 'ignore_compare_config'
                            ) {



                                


                                // Calculate fee based on duration from created_at to lastest_time_the_same
                                // If power is OFF, CPU and RAM = 0 for fee calculation
                                $feeCpu = ($lastUsage->power_state === 'POWERED_OFF') ? 0 : $lastUsage->cpu;
                                $feeRam = ($lastUsage->power_state === 'POWERED_OFF') ? 0 : $lastUsage->ram_gb;
                                
                                // Count chargeable IPs (free local IPs + 1 free internet IP)
                                $chargeableIpCount = VpsUsageFeeService::countChargeableIPs($lastUsage->list_ip_address);
                                
                                $calculatedFee = VpsUsageFeeService::calculateFee(
                                    $lastPricingConfig,
                                    $feeCpu,
                                    $feeRam,
                                    $lastUsage->disk_gb,
                                    $durationMinutes,
                                    $chargeableIpCount
                                );

                                $mUpdate = [
                                    'count_update_status' => DB::raw('count_update_status + 1'),
                                    'lastest_time_the_same' => now(),
                                    'timestamp_minute' => now()->startOfMinute(),
                                    'list_ip_address' => $listIpAddress,
                                    'last_found_ip' => $lastFoundIpTime,
                                    'calculated_fee' => $calculatedFee,
                                ];

                                //Nếu ko thấy IP, thì ko update IP rỗng:
                                if(!$listIpAddress){
                                    unset($mUpdate['list_ip_address']);
                                    unset($mUpdate['last_found_ip']);
                                }

                                // Just update the record
                                DB::table('vps_usages')
                                    ->where('id', $lastUsage->id)
                                    ->update($mUpdate);

                                $this->line("  ♻️  Updated vps_usages (count_update: " . ($lastUsage->count_update_status + 1) . ", Fee: " . number_format($calculatedFee, 0) . "K)");
                            } else {

                                // Config changed or 10+ minutes passed, insert new record (fee = 0 on first insert)
                                DB::table('vps_usages')->insert([
                                    'name' => $vm->name,
                                    'instance_id' => $instance->id,
                                    'vmware_vm_id' => $vm->vm,
                                    'cpu' => $vm->cpu,
                                    'ram_gb' => intval($vm->memory_gb),
                                    'disk_gb' => intval($vm->disk_gb),
                                    'user_id' => $instance->user_id,
                                    'timestamp_minute' => now()->startOfMinute(),
                                    'power_state' => $powerState,
                                    'bios_uuid' => $vm->bios_uuid,
                                    'instance_uuid' => $vm->instance_uuid,
                                    'full_info' => json_encode($vm),
                                    'price_config' => json_encode($currentPricingConfig),
                                    'calculated_fee' => 0,
                                    'status' => 1,
                                    'count_update_status' => 0,
                                    'lastest_time_the_same' => now(),
                                    'list_ip_address' => $listIpAddress,
                                    'mac_address' => implode(',', $vm->mac_addresses ?? []),
                                    'last_found_ip' => $lastFoundIpTime,
                                    'created_at' => now(),
                                ]);

                                if ($pricingHasChanged) {
                                    $this->line("  💰 Pricing config changed! Inserted new vps_usages snapshot");
                                } else {
                                    $this->line("  📊 Inserted vps_usages snapshot (config changed or 10+ min passed)");
                                }
                            }
                        } else {
                            // No previous record, insert new one - calculated_fee is 0 on first insert
                            DB::table('vps_usages')->insert([
                                'name' => $vm->name,
                                'instance_id' => $instance->id,
                                'vmware_vm_id' => $vm->vm,
                                'cpu' => $vm->cpu,
                                'ram_gb' => intval($vm->memory_gb),
                                'disk_gb' => intval($vm->disk_gb),
                                'user_id' => $instance->user_id,
                                'timestamp_minute' => now()->startOfMinute(),
                                'power_state' => $powerState,
                                'bios_uuid' => $vm->bios_uuid,
                                'instance_uuid' => $vm->instance_uuid,
                                'full_info' => json_encode($vm),
                                'price_config' => json_encode($currentPricingConfig),
                                'calculated_fee' => 0,
                                'status' => 1,
                                'count_update_status' => 0,
                                'lastest_time_the_same' => now(),
                                'list_ip_address' => $listIpAddress,
                                'mac_address' => implode(',', $vm->mac_addresses ?? []),
                                'last_found_ip' => $lastFoundIpTime,
                                'created_at' => now(),
                            ]);
                            $this->line("  📊 Inserted vps_usages snapshot (first record)");
                        }

                        // Check if config changed - only insert to history if it did
                        $configHash = md5(json_encode([
                            'cpu' => $vm->cpu,
                            'ram_gb' => intval($vm->memory_gb),
                            'disk_gb' => intval($vm->disk_gb),
                            'power_state' => $powerState,
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
                            // Calculate price_per_minute from daily fee
                            $dailyFee = VpsUsageFeeService::calculateDailyFee(
                                $currentPricingConfig,
                                $vm->cpu,
                                intval($vm->memory_gb),
                                intval($vm->disk_gb),
                                VpsUsageFeeService::countChargeableIPs($listIpAddress)
                            );
                            $pricePerMinute = $dailyFee / 1440; // 1440 minutes per day
                            
                            DB::table('vps_instance_config_histories')->insert([
                                'instance_id' => $instance->id,
                                'name' => $vm->name,
                                'vmware_vm_id' => $vm->vm,
                                'cpu' => $vm->cpu,
                                'ram_gb' => intval($vm->memory_gb),
                                'disk_gb' => intval($vm->disk_gb),
                                'power_state' => $powerState,
                                'bios_uuid' => $vm->bios_uuid,
                                'instance_uuid' => $vm->instance_uuid,
                                'price_per_minute' => $pricePerMinute,
                                'full_info' => json_encode($vm),
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

    /**
     * Get IP addresses from MAC addresses using ip_mac.json file
     *
     * @param object $vm VM object from getVMListV2() containing mac_addresses array
     * @return array ['list_ip_address' => 'ip1,ip2,...', 'last_found_ip' => datetime]
     */
    private function getIpFromMacAddress($vm): array
    {
        $result = [
            'list_ip_address' => '',
            'last_found_ip' => '',
        ];

        // Path to IP/MAC mapping file
        $ipMacFile = base_path('task-cli/scan_ip_mac_python/ip_mac.json');

        // Check if file exists
        if (!file_exists($ipMacFile)) {
            $this->warn("    ⚠️  IP/MAC file not found: $ipMacFile");
            return $result;
        }

        // Read and parse JSON file
        $jsonContent = file_get_contents($ipMacFile);
        $ipMacData = json_decode($jsonContent, true);

        if (!isset($ipMacData['results']) || !is_array($ipMacData['results'])) {
            $this->warn("    ⚠️  Invalid IP/MAC file format");
            return $result;
        }

        // Get file modification time as datetime
        $fileModTime = filemtime($ipMacFile);
        if ($fileModTime) {
            $result['last_found_ip'] = \Carbon\Carbon::createFromTimestamp($fileModTime)->setTimezone('Asia/Ho_Chi_Minh');
        }

        // Extract MAC addresses from VM object
        // getVMListV2() returns mac_addresses as direct array
        $vmMacAddresses = [];
        if (isset($vm->mac_addresses) && is_array($vm->mac_addresses)) {
            foreach ($vm->mac_addresses as $mac) {
                if (!empty($mac)) {
                    // Normalize MAC: lowercase and convert dashes to colons
                    $normalizedMac = strtolower(str_replace('-', ':', $mac));
                    $vmMacAddresses[] = $normalizedMac;
                }
            }
        }

        if (empty($vmMacAddresses)) {
            $this->line("    ℹ️  No MAC addresses found in VM NICs");
            return $result;
        }

        // Match MAC addresses with IPs from file
        $foundIps = [];
        foreach ($ipMacData['results'] as $ip => $mac) {
            // Normalize MAC from file: lowercase and convert dashes to colons
            $normalizedFileMac = strtolower(str_replace('-', ':', $mac));
            if (in_array($normalizedFileMac, $vmMacAddresses)) {
                $foundIps[] = $ip;
            }
        }

        if (!empty($foundIps)) {
            // Sort IPs for consistency
            sort($foundIps);
            $result['list_ip_address'] = implode(',', $foundIps);
            $this->line("    ✓ Found IPs: " . $result['list_ip_address']);
        } else {
            $this->line("    ℹ️  No matching IPs found for VM MACs");
        }

        return $result;
    }

    /**
     * Get MAC addresses from VM NICs
     *
     * @param object $vmInfo VM hardware info containing NICs
     * @return string MAC addresses as comma-separated string (e.g., "00:50:56:aa:bb:cc,00:50:56:dd:ee:ff")
     */
    private function getMacAddresses($vmInfo): string
    {
        $macAddresses = [];

        if (isset($vmInfo->nics) && is_array($vmInfo->nics)) {
            foreach ($vmInfo->nics as $nic) {
                if (isset($nic->mac_address) && !empty($nic->mac_address)) {
                    $macAddresses[] = $nic->mac_address;
                }
            }
        }

        return implode(',', $macAddresses);
    }
}
