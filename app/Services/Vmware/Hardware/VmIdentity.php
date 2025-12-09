<?php

namespace App\Services\Vmware\Hardware;

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
