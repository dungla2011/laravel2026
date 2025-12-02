<?php

require_once __DIR__.'/index.php';
if (! headers_sent()) {
    header_remove();
}

header('Content-Type:image/jpeg');
echo 'test NOT OK';
