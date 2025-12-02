<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

echo "ABC";
//require_once "/var/www/html/public/index.php";
file_put_contents('/var/glx/weblog/draw.io.log', time());
//
//if($xml = request('xml')){
//    $file = "/var/www/html/public/tool/testing/drawio1.xml";
//    file_put_contents($file, trim($xml));
//    echo "FileSize:".filesize($file) . " " .  nowyh();
//}
