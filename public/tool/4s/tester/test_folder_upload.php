<?php

$_SERVER['SERVER_NAME'] = '4share.vn';

require "/var/www/html/public/index.php";


$folder = '/var/ufile/test_create_111/222/333';
exec('rm -rf /var/ufile/test_create_111');
mkdir($folder, 0777, true);
if(!file_exists($folder)){
    die("Can't create folder");
}
echo "folder_create_ok";
exec('rm -rf /var/ufile/test_create_111');

