<?php

namespace App\Services\Vmware\Hardware;

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
