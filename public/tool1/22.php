<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * VMware VM Hardware Classes
 */

// ==================== MEMORY CLASS ====================
class VmMemory {
    public int $size_MiB = 0;
    public int $hot_add_enabled = 0;
    public int $hot_add_increment_size_MiB = 0;
    public int $hot_add_limit_MiB = 0;

    public function __construct($obj = null) {
        if ($obj) {
            $this->size_MiB = $obj->size_MiB ?? 0;
            $this->hot_add_enabled = $obj->hot_add_enabled ?? 0;
            $this->hot_add_increment_size_MiB = $obj->hot_add_increment_size_MiB ?? 0;
            $this->hot_add_limit_MiB = $obj->hot_add_limit_MiB ?? 0;
        }
    }
}

// ==================== CPU CLASS ====================
class VmCpu {
    public int $count = 0;
    public int $cores_per_socket = 1;
    public int $hot_add_enabled = 0;
    public int $hot_remove_enabled = 0;

    public function __construct($obj = null) {
        if ($obj) {
            $this->count = $obj->count ?? 0;
            $this->cores_per_socket = $obj->cores_per_socket ?? 1;
            $this->hot_add_enabled = $obj->hot_add_enabled ?? 0;
            $this->hot_remove_enabled = $obj->hot_remove_enabled ?? 0;
        }
    }
}

// ==================== DISK SCSI INFO ====================
class VmDiskScsi {
    public int $bus = 0;
    public int $unit = 0;

    public function __construct($obj = null) {
        if ($obj) {
            $this->bus = $obj->bus ?? 0;
            $this->unit = $obj->unit ?? 0;
        }
    }
}

// ==================== DISK BACKING INFO ====================
class VmDiskBacking {
    public string $type = '';
    public string $vmdk_file = '';

    public function __construct($obj = null) {
        if ($obj) {
            $this->type = $obj->type ?? '';
            $this->vmdk_file = $obj->vmdk_file ?? '';
        }
    }
}

// ==================== DISK INFO ====================
class VmDiskInfo {
    public string $label = '';
    public string $type = '';
    public int $capacity = 0;
    public VmDiskScsi $scsi;
    public VmDiskBacking $backing;

    public function __construct($obj = null) {
        $this->scsi = new VmDiskScsi();
        $this->backing = new VmDiskBacking();

        if ($obj) {
            $this->label = $obj->label ?? '';
            $this->type = $obj->type ?? '';
            $this->capacity = $obj->capacity ?? 0;
            if (isset($obj->scsi)) {
                $this->scsi = new VmDiskScsi($obj->scsi);
            }
            if (isset($obj->backing)) {
                $this->backing = new VmDiskBacking($obj->backing);
            }
        }
    }
}

// ==================== NIC BACKING INFO ====================
class VmNicBacking {
    public string $type = '';
    public string $network = '';
    public string $network_name = '';

    public function __construct($obj = null) {
        if ($obj) {
            $this->type = $obj->type ?? '';
            $this->network = $obj->network ?? '';
            $this->network_name = $obj->network_name ?? '';
        }
    }
}

// ==================== NIC INFO ====================
class VmNicInfo {
    public string $label = '';
    public string $type = '';
    public string $mac_address = '';
    public string $mac_type = '';
    public string $state = '';
    public int $start_connected = 0;
    public int $allow_guest_control = 0;
    public int $wake_on_lan_enabled = 0;
    public int $upt_compatibility_enabled = 0;
    public int $pci_slot_number = 0;
    public VmNicBacking $backing;

    public function __construct($obj = null) {
        $this->backing = new VmNicBacking();

        if ($obj) {
            $this->label = $obj->label ?? '';
            $this->type = $obj->type ?? '';
            $this->mac_address = $obj->mac_address ?? '';
            $this->mac_type = $obj->mac_type ?? '';
            $this->state = $obj->state ?? '';
            $this->start_connected = $obj->start_connected ?? 0;
            $this->allow_guest_control = $obj->allow_guest_control ?? 0;
            $this->wake_on_lan_enabled = $obj->wake_on_lan_enabled ?? 0;
            $this->upt_compatibility_enabled = $obj->upt_compatibility_enabled ?? 0;
            $this->pci_slot_number = $obj->pci_slot_number ?? 0;
            if (isset($obj->backing)) {
                $this->backing = new VmNicBacking($obj->backing);
            }
        }
    }
}

// ==================== IDENTITY INFO ====================
class VmIdentity {
    public string $name = '';
    public string $instance_uuid = '';
    public string $bios_uuid = '';

    public function __construct($obj = null) {
        if ($obj) {
            $this->name = $obj->name ?? '';
            $this->instance_uuid = $obj->instance_uuid ?? '';
            $this->bios_uuid = $obj->bios_uuid ?? '';
        }
    }
}

// ==================== BOOT INFO ====================
class VmBoot {
    public string $type = 'BIOS';
    public int $delay = 0;
    public int $retry_delay = 10;
    public int $retry = 0;
    public int $enter_setup_mode = 0;

    public function __construct($obj = null) {
        if ($obj) {
            $this->type = $obj->type ?? 'BIOS';
            $this->delay = $obj->delay ?? 0;
            $this->retry_delay = $obj->retry_delay ?? 10;
            $this->retry = $obj->retry ?? 0;
            $this->enter_setup_mode = $obj->enter_setup_mode ?? 0;
        }
    }
}

// ==================== HARDWARE VERSION ====================
class VmHardware {
    public string $version = '';
    public string $upgrade_policy = '';
    public string $upgrade_status = '';

    public function __construct($obj = null) {
        if ($obj) {
            $this->version = $obj->version ?? '';
            $this->upgrade_policy = $obj->upgrade_policy ?? '';
            $this->upgrade_status = $obj->upgrade_status ?? '';
        }
    }
}

// ==================== MAIN VM HARDWARE INFO CLASS ====================
class VmHardwareInfo {
    public string $name = '';
    public string $guest_OS = '';
    public string $power_state = '';
    public VmMemory $memory;
    public VmCpu $cpu;
    public VmIdentity $identity;
    public VmBoot $boot;
    public VmHardware $hardware;
    public array $nics = []; // array of VmNicInfo
    public array $disks = []; // array of VmDiskInfo

    public function __construct($obj = null) {
        $this->memory = new VmMemory();
        $this->cpu = new VmCpu();
        $this->identity = new VmIdentity();
        $this->boot = new VmBoot();
        $this->hardware = new VmHardware();

        if ($obj) {
            $this->name = $obj->name ?? '';
            $this->guest_OS = $obj->guest_OS ?? '';
            $this->power_state = $obj->power_state ?? '';

            if (isset($obj->memory)) {
                $this->memory = new VmMemory($obj->memory);
            }
            if (isset($obj->cpu)) {
                $this->cpu = new VmCpu($obj->cpu);
            }
            if (isset($obj->identity)) {
                $this->identity = new VmIdentity($obj->identity);
            }
            if (isset($obj->boot)) {
                $this->boot = new VmBoot($obj->boot);
            }
            if (isset($obj->hardware)) {
                $this->hardware = new VmHardware($obj->hardware);
            }

            // Parse NICs array
            if (isset($obj->nics) && is_array($obj->nics)) {
                foreach ($obj->nics as $nicItem) {
                    if (isset($nicItem->value)) {
                        $this->nics[] = new VmNicInfo($nicItem->value);
                    }
                }
            }

            // Parse Disks array
            if (isset($obj->disks) && is_array($obj->disks)) {
                foreach ($obj->disks as $diskItem) {
                    if (isset($diskItem->value)) {
                        $this->disks[] = new VmDiskInfo($diskItem->value);
                    }
                }
            }
        }
    }

    /**
     * Get summary info
     */
    public function getSummary(): string {
        return "VM: {$this->name} | OS: {$this->guest_OS} | CPU: {$this->cpu->count} | Memory: {$this->memory->size_MiB}MB | NICs: " . count($this->nics) . " | Disks: " . count($this->disks);
    }

    /**
     * Get total disk capacity in GB
     */
    public function getTotalDiskGB(): float {
        $total = 0;
        foreach ($this->disks as $disk) {
            $total += $disk->capacity;
        }
        return round($total / (1024 * 1024 * 1024), 2);
    }

    /**
     * Get all MAC addresses
     */
    public function getMacAddresses(): array {
        $macs = [];
        foreach ($this->nics as $nic) {
            $macs[] = [
                'mac' => $nic->mac_address,
                'label' => $nic->label,
                'network' => $nic->backing->network_name,
                'state' => $nic->state
            ];
        }
        return $macs;
    }
}

/**
 * VMware VM 리스트 추출 라이브러리
 * getVMList, getHostList 등의 함수 모음
 */

class VmwareHelper {
    private static $sid = null;
    private static $domain = null;

    /**
     * VMware vCenter에 로그인하여 session ID 획득
     * @param string $domain vCenter 도메인/IP
     * @param string $uid 사용자명 (예: administrator@vsphere.local)
     * @param string $pw 비밀번호
     * @return bool 로그인 성공 여부
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
     * Host 리스트 조회
     * @return array Host 목록
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
     * VM 리스트 조회
     * @param string|null $powerState 전원 상태 필터 (POWERED_ON, POWERED_OFF, SUSPENDED)
     * @param string|null $orderBy 정렬 방식 ('name' or null)
     * @param string|null $filterString URL 필터 문자열 (예: ?filter.hosts.1=host-123)
     * @return array VM 목록
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
            // 전원 상태 필터
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
     * 전원이 켜진 VM만 조회
     * @param string|null $orderBy 정렬 방식
     * @param string|null $filterString 필터 문자열
     * @return array POWERED_ON 상태의 VM 목록
     */
    public static function getVMListPowerOn($orderBy = null, $filterString = null) {
        return self::getVMList('POWERED_ON', $orderBy, $filterString);
    }

    /**
     * VM 상세 정보 조회
     * @param string $vmId VM ID (예: vm-12345)
     * @return VmHardwareInfo|null VM 상세 정보
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
     * 모든 Host의 모든 VM 조회
     * @return array 모든 VM 목록
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
     * DataStore 리스트 조회
     * @param string $nameFilter 이름 필터 (선택사항)
     * @return array DataStore 목록
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

// ==================== DEMO USAGE ====================
// Comment out demo code - only runs when executed directly
if (php_sapi_name() === 'cli' && basename($argv[0] ?? '') === basename(__FILE__)) {
    echo "=== VMware Helper Demo ===\n\n";

    // 1. 로그인
    $domain = "10.0.1.8";  // vCenter IP/Domain
    $domain = "vc5.glx";  // vCenter IP/Domain
    $uid = "administrator@vsphere.local";
    // $pw = env("VC_P")."0987";
    $pw = "Cloud!@)((0987";

    if (!VmwareHelper::loginVC($domain, $uid, $pw)) {
        die("❌ Login failed\n");
    }

    echo "\n--- Host List ---\n";
    $hosts = VmwareHelper::getHostList();
    foreach ($hosts as $host) {
        echo "Host: {$host->name} (ID: {$host->host})\n";
    }

    echo "\n--- All VMs (Powered On) ---\n";
    $vms = VmwareHelper::getVMListPowerOn();
    foreach ($vms as $vm) {
        echo "VM: {$vm->name} (ID: {$vm->vm}, Power: {$vm->power_state})\n";
    }

    echo "\n--- VM Detail Info (with Classes) ---\n";
    if (!empty($vms)) {
        $firstVm = reset($vms);
        $vmInfo = VmwareHelper::getVMInfo($firstVm->vm);

        if ($vmInfo instanceof VmHardwareInfo) {
            // ✅ 이제 property에 직접 접근 가능!
            echo "Name: {$vmInfo->name}\n";
            echo "OS: {$vmInfo->guest_OS}\n";
            echo "Power: {$vmInfo->power_state}\n";
            echo "\n--- Memory Info ---\n";
            echo "  Size: {$vmInfo->memory->size_MiB} MB\n";
            echo "  Hot Add Enabled: {$vmInfo->memory->hot_add_enabled}\n";
            echo "  Hot Add Limit: {$vmInfo->memory->hot_add_limit_MiB} MB\n";


            echo "\n--- CPU Info ---\n";
            echo "  Count: {$vmInfo->cpu->count}\n";
            echo "  Cores per socket: {$vmInfo->cpu->cores_per_socket}\n";
            echo "  Hot Add: {$vmInfo->cpu->hot_add_enabled}\n";

            echo "\n--- Identity Info ---\n";
            echo "  Instance UUID: {$vmInfo->identity->instance_uuid}\n";
            echo "  BIOS UUID: {$vmInfo->identity->bios_uuid}\n";

            echo "\n--- Boot Info ---\n";
            echo "  Type: {$vmInfo->boot->type}\n";
            echo "  Delay: {$vmInfo->boot->delay}ms\n";
            echo "  Retry Delay: {$vmInfo->boot->retry_delay}ms\n";

            echo "\n--- Hardware Version ---\n";
            echo "  Version: {$vmInfo->hardware->version}\n";
            echo "  Upgrade Policy: {$vmInfo->hardware->upgrade_policy}\n";

            echo "\n--- Disks ---\n";
            echo "Total Size: " . $vmInfo->getTotalDiskGB() . " GB\n";
            foreach ($vmInfo->disks as $idx => $disk) {
                echo "  Disk $idx: {$disk->label} ({$disk->type})\n";
                echo "    - Capacity: " . ($disk->capacity / (1024*1024*1024)) . " GB\n";
                echo "    - SCSI: Bus {$disk->scsi->bus}, Unit {$disk->scsi->unit}\n";
                echo "    - File: {$disk->backing->vmdk_file}\n";
            }

            echo "\n--- NICs ---\n";
            foreach ($vmInfo->getMacAddresses() as $nic) {
                echo "  {$nic['label']}: {$nic['mac']}\n";
                echo "    - Network: {$nic['network']}\n";
                echo "    - State: {$nic['state']}\n";
            }

            echo "\n--- Summary ---\n";
            echo $vmInfo->getSummary() . "\n";
        }
    }

    echo "\n--- DataStore List ---\n";
    $datastores = VmwareHelper::getDataStoreList();
    foreach ($datastores as $ds) {
        echo "DataStore: {$ds->name}\n";
    }
}
