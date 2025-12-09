<?php

namespace App\Services\Vmware\Hardware;

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
