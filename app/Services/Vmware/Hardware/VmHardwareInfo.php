<?php

namespace App\Services\Vmware\Hardware;

/**
 * Main VM Hardware Information class
 * Contains all hardware details for a virtual machine
 */
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
     * Get summary information
     * 
     * @return string Summary string
     */
    public function getSummary(): string {
        return "VM: {$this->name} | OS: {$this->guest_OS} | CPU: {$this->cpu->count} | Memory: {$this->memory->size_MiB}MB | NICs: " . count($this->nics) . " | Disks: " . count($this->disks);
    }

    /**
     * Get total disk capacity in GB
     * 
     * @return float Total disk size in GB
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
     * 
     * @return array Array of MAC address information
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
