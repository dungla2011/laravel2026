<?php

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'test2023.mytree.vn';

require '/var/www/html/public/index.php';


$file = "/var/glx/weblog/m2.json";

$chanel = 'vovo';
$flog = "/var/glx/weblog/zalo-test-mess-log_$chanel.txt";

if (file_exists($file)) {
    $data = file_get_contents($file);
    $mm = json_decode($data, true);
    $cc = 0;
    if (is_array($mm)) {

        foreach ($mm AS $m){

            saveMessZlToDb(json_encode($m), $chanel);

        }
    } else {
        echo "NOT ARRAY";
    }
} else {
    echo "FILE NOT EXISTS";
}
