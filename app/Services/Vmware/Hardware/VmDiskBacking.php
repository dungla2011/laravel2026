<?php

namespace App\Services\Vmware\Hardware;

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
