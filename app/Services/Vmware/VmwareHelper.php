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

        $ch = curl_init("https://$domain/rest/com/vmware/cis/session");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "");
        curl_setopt($ch, CURLOPT_USERPWD, $uid . ':' . $pw);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl/7.29.0');

        $ret = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            echo "❌ Login failed (HTTP $httpCode)\n";
            return false;
        }

        $out = json_decode($ret);
        if (!isset($out->value) || !is_string($out->value)) {
            echo "❌ Invalid session response\n";
            return false;
        }

        self::$sid = $out->value;
        self::$domain = $domain;
        echo "✓ Login successful. SID: " . substr($out->value, 0, 20) . "...\n";
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
}
