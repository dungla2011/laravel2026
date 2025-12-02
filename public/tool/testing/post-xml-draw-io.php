<?php
require_once "/var/www/html/public/index.php";
if($xml = request('xml')){
    $file = "/var/www/html/public/tool/testing/drawio1.xml";
    file_put_contents($file, trim($xml));
    echo "FileSize:".filesize($file) . " " .  nowyh();
}
