<?php

namespace App\Services\Vmware\Hardware;

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
