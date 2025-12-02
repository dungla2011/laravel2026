<?php

$chanel = $_REQUEST['channel_name'] ?? '';
$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = "test2023.mytree.vn";

if($chanel == 'anh_taxi') {
    $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = "taxi.mytree.vn";
}

require "/var/www/html/public/index.php";
require_once "/var/www/html/public/tool1/_site/taxi/lib_taxi.php";
$chanel = '';
if($chanel = request('channel_name')) {

}

$flog = "/var/glx/weblog/zalo-test-mess-log_$chanel.txt";
$errorLog = '/var/glx/weblog/zalo-test-mess-log.error.txt';
// Get JSON
$mess = file_get_contents('php://input');
file_put_contents($flog, date("Y-m-d H:i:s") ." # MSG = ". $mess . "\n\n", FILE_APPEND);
file_put_contents($flog,  "# CHANEL = ". serialize($_REQUEST) . "\n\n", FILE_APPEND);


$mm = json_decode($mess, true);


// Save message to CrmMessage model
try {



    saveMessZlToDb($mess, $chanel);

} catch (\Exception $e) {
    echo "Error message: " . $e->getMessage();
    ol5( " # *** Error: " . $e->getMessage() . "\n");
}
