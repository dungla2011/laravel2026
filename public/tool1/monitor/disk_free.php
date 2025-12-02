<?php

use App\Components\CDisk;

require_once '../../index.php';

$free = CDisk::getFreeDiskInFilePath('/');

echo "\n FREE all: ".ByteSize($free);
if (is_numeric($free)) {
    if ($free > 5 * _GB) {
        echo '-free_ok_now:'.ByteSize($free);
    } else {
        echo '-free_not_ok:'.ByteSize($free);
    }
}

$free = CDisk::getFreeDiskInFilePath('/mnt/glx');

echo "\n FREE memdisk all: ".ByteSize($free);
if (is_numeric($free)) {
    if ($free > 100 * _MB) {
        echo '-free_ok_now:'.ByteSize($free);
    } else {
        echo '-free_not_ok:'.ByteSize($free);
    }
}
