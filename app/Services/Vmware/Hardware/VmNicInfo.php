<?php

namespace App\Services\Vmware\Hardware;

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
