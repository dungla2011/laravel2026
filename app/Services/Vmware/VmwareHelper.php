<?php

namespace App\Services\Vmware;

use App\Services\Vmware\Hardware\{
    VmHardwareInfo, VmMemory, VmCpu, VmIdentity, VmBoot, VmHardware,
    VmNicInfo, VmDiskInfo
};

/**
 * VMware vCenter Integration Service
 *
 * Provides complete API wrapper for VMware vSphere REST API
 * Handles authentication, VM discovery, and hardware information retrieval
 */
class VmwareHelper {
    private static $sid = null;
    private static $domain = null;

    /**
     * Login to VMware vCenter and obtain session ID
     *
     * @param string $domain vCenter domain/IP
     * @param string $uid Username (e.g., administrator@vsphere.local)
     * @param string $pw Password
     * @return bool Login success status
     */
    public static function loginVC($domain = null, $uid = null, $pw = null) {
        if (!$domain || !$uid || !$pw) {
            echo "❌ Domain, UID, PW required\n";
            return false;
        }

        echo "🔐 Attempting login to https://$domain/rest/com/vmware/cis/session\n";

        $ch = curl_init("https://$domain/rest/com/vmware/cis/session");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "");
        curl_setopt($ch, CURLOPT_USERPWD, $uid . ':' . $pw);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl/7.29.0');
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        $ret = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        echo "Response length: " . strlen($ret) . " bytes\n";
        echo "HTTP Code: $httpCode\n";

        if ($curlError) {
            echo "❌ cURL error: $curlError\n";
            return false;
        }

        if ($httpCode !== 200) {
            echo "❌ Login failed (HTTP $httpCode)\n";
            echo "Response body: " . $ret . "\n";
            return false;
        }

        if (empty($ret)) {
            echo "❌ Empty response from vCenter\n";
            return false;
        }

        $out = json_decode($ret, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "❌ JSON decode error: " . json_last_error_msg() . "\n";
            echo "Response: " . $ret . "\n";
            return false;
        }

        if (!isset($out['value']) || !is_string($out['value'])) {
            echo "❌ Invalid session response\n";
            echo "Response: " . print_r($out, true) . "\n";
            return false;
        }

        self::$sid = $out['value'];
        self::$domain = $domain;
        echo "✓ Login successful. SID: " . substr($out['value'], 0, 20) . "...\n";
        return true;
    }

    /**
     * Get list of hosts
     *
     * @return array List of hosts
     */
    public static function getHostList() {
        if (!self::$sid || !self::$domain) {
            echo "❌ Not logged in\n";
            return [];
        }

        $ch = curl_init("https://" . self::$domain . "/rest/vcenter/host");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["vmware-api-session-id:" . self::$sid]);

        $output = curl_exec($ch);
        $info = json_decode($output);
        curl_close($ch);

        if (!$info || !isset($info->value)) {
            echo "❌ Failed to get host list\n";
            return [];
        }

        echo "✓ Host count: " . count($info->value) . "\n";
        return $info->value;
    }

    /**
     * Get list of VMs
     *
     * @param string|null $powerState Filter by power state (POWERED_ON, POWERED_OFF, SUSPENDED)
     * @param string|null $orderBy Sort order ('name' or null)
     * @param string|null $filterString URL filter string (e.g., ?filter.hosts.1=host-123)
     * @return array List of VMs
     */
    public static function getVMList($powerState = null, $orderBy = null, $filterString = null) {
        if (!self::$sid || !self::$domain) {
            echo "❌ Not logged in\n";
            return [];
        }

        $url = "https://" . self::$domain . "/rest/vcenter/vm" . ($filterString ?? '');

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["vmware-api-session-id:" . self::$sid]);

        $output = curl_exec($ch);
        $info = json_decode($output);
        curl_close($ch);

        if (!$info || !isset($info->value)) {
            echo "❌ Failed to get VM list\n";
            return [];
        }

        $result = [];
        foreach ($info->value as $vm) {
            // Filter by power state
            if ($powerState && isset($vm->power_state) && $vm->power_state !== $powerState) {
                continue;
            }

            $vmId = str_replace("vm-", '', $vm->vm);
            $key = ($orderBy === 'name') ? ($vm->name . '_' . $vmId) : $vmId;
            $result[$key] = $vm;
        }

        ksort($result);
        echo "✓ VM count: " . count($result) . "\n";
        return $result;
    }

    /**
     * Get only powered-on VMs
     *
     * @param string|null $orderBy Sort order
     * @param string|null $filterString Filter string
     * @return array List of POWERED_ON VMs
     */
    public static function getVMListPowerOn($orderBy = null, $filterString = null) {
        return self::getVMList('POWERED_ON', $orderBy, $filterString);
    }

    /**
     * Get VM detailed information
     *
     * @param string $vmId VM ID (e.g., vm-12345)
     * @return VmHardwareInfo|null VM hardware information
     */
    public static function getVMInfo($vmId) {
        if (!self::$sid || !self::$domain) {
            echo "❌ Not logged in\n";
            return null;
        }

        $url = "https://" . self::$domain . "/rest/vcenter/vm/$vmId";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["vmware-api-session-id:" . self::$sid]);

        $output = curl_exec($ch);
        $info = json_decode($output);
        curl_close($ch);

        if (!$info || !isset($info->value)) {
            echo "❌ Failed to get VM info for $vmId\n";
            return null;
        }

        // Convert stdClass to VmHardwareInfo
        return new VmHardwareInfo($info->value);
    }

    /**
     * Get all VMs from all hosts
     *
     * @return array All VMs across all hosts
     */
    public static function getVMListAllHosts() {
        $hosts = self::getHostList();
        $allVMs = [];

        foreach ($hosts as $host) {
            $filterString = "?filter.hosts.1=" . $host->host . "&filter.power_states.1=POWERED_ON";
            $vms = self::getVMList(null, null, $filterString);
            $allVMs = array_merge($allVMs, $vms);
        }

        echo "✓ Total VMs from all hosts: " . count($allVMs) . "\n";
        return $allVMs;
    }

    /**
     * Get list of datastores
     *
     * @param string $nameFilter Name filter (optional)
     * @return array List of datastores
     */
    public static function getDataStoreList($nameFilter = '') {
        if (!self::$sid || !self::$domain) {
            echo "❌ Not logged in\n";
            return [];
        }

        $url = "https://" . self::$domain . "/rest/vcenter/datastore";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["vmware-api-session-id:" . self::$sid]);

        $output = curl_exec($ch);
        $info = json_decode($output);
        curl_close($ch);

        if (!$info || !isset($info->value)) {
            echo "❌ Failed to get DataStore list\n";
            return [];
        }

        $result = [];
        foreach ($info->value as $ds) {
            if ($nameFilter && strpos($ds->name, $nameFilter) === false) {
                continue;
            }
            $result[] = $ds;
        }

        echo "✓ DataStore count: " . count($result) . "\n";
        return $result;
    }

    /**
     * Get VM boot time (uptime) from event logs
     *
     * @param string $vmId VM ID
     * @return string|null Boot time (ISO 8601 format) or null if not found
     */
    public static function getVMBootTime($vmId) {
        if (!self::$sid || !self::$domain) {
            echo "❌ Not logged in\n";
            return null;
        }

        // Query last VmPoweredOnEvent for this VM
        $url = "https://" . self::$domain . "/rest/vcenter/event?filter.types=VmPoweredOnEvent&filter.entity.type=VirtualMachine&filter.entity.id=" . urlencode($vmId);

        echo "🔍 Fetching boot time for VM: $vmId\n";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["vmware-api-session-id:" . self::$sid]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $output = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            echo "❌ cURL error: $curlError\n";
            return null;
        }

        if ($httpCode !== 200) {
            echo "❌ Failed to get boot time (HTTP $httpCode)\n";
            return null;
        }

        $info = json_decode($output, true);
        if (!$info || !isset($info['value']) || !is_array($info['value'])) {
            echo "⚠️  No boot events found for VM: $vmId\n";
            return null;
        }

        // Get the most recent VmPoweredOnEvent
        if (empty($info['value'])) {
            echo "⚠️  No VmPoweredOnEvent in response\n";
            return null;
        }

        // Events should be ordered by timestamp, get the first one (most recent)
        $bootEvent = $info['value'][0];
        $bootTime = $bootEvent['created_time'] ?? null;

        if (!$bootTime) {
            echo "⚠️  Boot time not found in event\n";
            return null;
        }

        echo "✓ VM boot time: $bootTime\n";
        return $bootTime;
    }

    /**
     * Get VM uptime in minutes
     *
     * @param string $vmId VM ID
     * @return int|null Uptime in minutes, or null if cannot determine
     */
    public static function getVMUptime($vmId) {
        $bootTime = self::getVMBootTime($vmId);

        if (!$bootTime) {
            return null;
        }

        try {
            $bootDateTime = new \DateTime($bootTime, new \DateTimeZone('UTC'));
            $now = new \DateTime('now', new \DateTimeZone('UTC'));
            $diff = $now->diff($bootDateTime);

            // Calculate total minutes
            $uptimeMinutes = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;

            echo "✓ VM uptime: $uptimeMinutes minutes (" . floor($uptimeMinutes / 60) . "h " . ($uptimeMinutes % 60) . "m)\n";
            return $uptimeMinutes;
        } catch (\Exception $e) {
            echo "❌ Error calculating uptime: " . $e->getMessage() . "\n";
            return null;
        }
    }

    /**
     * Get VM list using Python pyVmomi script
     *
     * Executes Python script to fetch VMs directly from ESXi hosts
     * More reliable than REST API for getting complete VM information
     *
     * @param string $outputFile JSON file path for output (default: /var/glx/weblog/vps_glx.json)
     * @return array Array of VMs from JSON file, empty array on failure
     */
    public static function getVMListV2($outputFile = '/var/glx/weblog/vps_glx_list_v3.json') {
        $pythonScript = '/var/www/html/task-cli/vmware/get-vm-info-pyVmomi.py';

        // Verify Python script exists
        if (!file_exists($pythonScript)) {
            echo "❌ Python script not found: $pythonScript\n";
            return [];
        }

        // Handle old JSON file - archive to zip if zip is old enough
        if (file_exists($outputFile)) {
            $zipFile = "/var/glx/weblog/vps_glx_list_v3.zip";
            $shouldArchive = true;

            // Check if zip file exists and is recent (within 180 minutes)
            if (file_exists($zipFile)) {
                $zipModTime = filemtime($zipFile);
                $zipAge = (time() - $zipModTime) / 60; // in minutes

                if ($zipAge < 180) {
                     $shouldArchive = false;
                     echo "⏭️  Zip file is recent ({$zipAge} min old), skipping archive\n";
                }
            }

            if ($shouldArchive) {
                // Rename old file with timestamp
                $timestamp = date('Y-m-d_H-i-s');
                $archivedFile = $outputFile . '.' . $timestamp;

                if (@rename($outputFile, $archivedFile)) {
                    echo "📝 Renamed to: {$archivedFile}\n";

                    // Add to zip archive (CREATE only - don't OVERWRITE to keep all old files)
                    $zip = new \ZipArchive();
                    if ($zip->open($zipFile, \ZipArchive::CREATE) === true) {
                        if ($zip->addFile($archivedFile, basename($archivedFile))) {
                            $zip->close();
                            echo "📦 Archived to: {$zipFile}\n";

                            // Delete the renamed file
                            if (@unlink($archivedFile)) {
                                echo "🗑️  Deleted archived file: {$archivedFile}\n";
                            }
                        } else {
                            echo "⚠️  Failed to add file to zip\n";
                            $zip->close();
                        }
                    } else {
                        echo "⚠️  Could not create/open zip file: {$zipFile}\n";
                    }
                } else {
                    echo "⚠️  Could not rename old file: {$outputFile}\n";
                }
            }
        }

        // Execute Python script with realtime output
        // Use -u flag to make Python unbuffered (flush output immediately)
        $command = "/var/www/html/task-cli/vmware/vmware_env/bin/python3 -u {$pythonScript} output={$outputFile}";
        echo "🔄 Executing: {$command}\n";
        echo "📡 Streaming output:\n";

        // Use passthru for direct realtime output streaming
        $returnCode = 0;
        passthru($command, $returnCode);

        // Check if execution was successful
        if ($returnCode !== 0) {
            echo "❌ Python script failed with return code: {$returnCode}\n";
            return [];
        }

        // Check if JSON file was created
        if (!file_exists($outputFile)) {
            echo "❌ Output file not created: {$outputFile}\n";
            return [];
        }

        // Verify file was created recently (within 15 seconds)
        $fileModTime = filemtime($outputFile);
        $currentTime = time();
        $fileAge = $currentTime - $fileModTime;

        if ($fileAge > 15) {
            echo "❌ Output file is stale (created {$fileAge} seconds ago, max 15s allowed)\n";
            echo "   File path: {$outputFile}\n";
            echo "   File mtime: " . date('Y-m-d H:i:s', $fileModTime) . "\n";
            echo "   Current time: " . date('Y-m-d H:i:s', $currentTime) . "\n";
            return [];
        }

        echo "✓ File created {$fileAge} seconds ago (fresh data)\n";

        // Read and parse JSON file
        try {
            $jsonContent = file_get_contents($outputFile);
            $vmsData = json_decode($jsonContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                echo "❌ JSON parse error: " . json_last_error_msg() . "\n";
                return [];
            }

            if (!is_array($vmsData)) {
                echo "❌ Invalid JSON format (not an array)\n";
                return [];
            }

            // Convert array data to objects compatible with getVMInfo output
            $vmsObjects = [];
            foreach ($vmsData as $vmData) {
                // Create object with vm_id property for compatibility
                $vmObj = (object)$vmData;
                // Also set 'vm' property (required by SyncVmwareInstancesCommand)
                $vmObj->vm = $vmData['vm_id'];
                $vmsObjects[] = $vmObj;
            }

            echo "✅ Loaded " . count($vmsObjects) . " VMs from {$outputFile}\n";

            {
                // getch(".....");
                // Log old JSON content to DB if vps_logs table exists
                $newContent = @file_get_contents($outputFile);
                if ($newContent) {
                    try {
                        //  getch("....2.");
                        if (\Illuminate\Support\Facades\Schema::hasTable('vps_logs') &&
                            \Illuminate\Support\Facades\Schema::hasColumn('vps_logs', 'logs')) {
                            $shouldInsert = true;
                        // getch("....21.");
                            // Check last row — only insert if older than 3 hours
                            $lastRow = \Illuminate\Support\Facades\DB::table('vps_logs')
                                ->orderByDesc('id')
                                ->first();
                            if ($lastRow && !empty($lastRow->created_at)) {
                                $lastTime = strtotime($lastRow->created_at);
                                $ageMinutes = (time() - $lastTime) / 60;
                                if ($ageMinutes < 180) {
                                    $shouldInsert = false;
                                    echo "⏭️  vps_logs last row is " . round($ageMinutes) . " min old, skipping insert\n";
                                }
                            }

                            if ($shouldInsert) {
                                //  getch(".....3 ");
                                $now = date('Y-m-d H:i:s');
                                $inserted = \Illuminate\Support\Facades\DB::table('vps_logs')->insert([
                                    'logs'       => $newContent
                                ]);
                                if ($inserted) {
                                    echo "📝 Logged JSON to vps_logs table\n";
                                } else {
                                    echo "⚠️  Insert to vps_logs returned false\n";
                                }
                                //   getch(".....3 1");
                            }
                            // getch(".....3 2");
                        }
                    } catch (\Exception $e) {
                        echo "⚠️  Could not log to vps_logs: " . $e->getMessage() . "\n";
                        // getch(".....3 3");
                    }
                }
            }

            return $vmsObjects;
        } catch (\Exception $e) {
            echo "❌ Error reading/parsing JSON: " . $e->getMessage() . "\n";
            return [];
        }
    }
}

