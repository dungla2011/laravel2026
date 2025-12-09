<?php

namespace App\Services\Vmware\Hardware;

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
