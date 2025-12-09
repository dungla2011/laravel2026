<?php

namespace App\Services\Vmware\Hardware;

class VmDiskInfo {
    public string $label = '';
    public string $type = '';
    public int $capacity = 0;
    public VmDiskScsi $scsi;
    public VmDiskBacking $backing;

    public function __construct($obj = null) {
        $this->scsi = new VmDiskScsi();
        $this->backing = new VmDiskBacking();

        if ($obj) {
            $this->label = $obj->label ?? '';
            $this->type = $obj->type ?? '';
            $this->capacity = $obj->capacity ?? 0;
            if (isset($obj->scsi)) {
                $this->scsi = new VmDiskScsi($obj->scsi);
            }
            if (isset($obj->backing)) {
                $this->backing = new VmDiskBacking($obj->backing);
            }
        }
    }
}
