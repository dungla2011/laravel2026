<?php

namespace App\Services\Vmware\Hardware;

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
