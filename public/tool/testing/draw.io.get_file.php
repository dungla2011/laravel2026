<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/xml; charset=utf-8");
//require_once "/var/www/html/public/index.php";
$file = "/var/www/html/public/tool/testing/drawio1.xml";
echo file_get_contents($file);
