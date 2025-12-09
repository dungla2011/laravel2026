<?php

namespace App\Services\Vmware\Hardware;

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
