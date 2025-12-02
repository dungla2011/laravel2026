<?php

system('php artisan optimize:clear');

//require_once __DIR__.'/public/index.php';


require_once __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);


$dbInfo = \App\Components\Helper1::getDBInfo();

$dbName = $dbInfo['database'];
$dbHost = $dbInfo['host'];
$dbUsername = $dbInfo['username'];
$dbPw = $dbInfo['password'];

if (! $dbName) {
    exit("\n\n*** Error: not db name? $dbName");
}


dumpDb1($dbName,$dbHost, $dbUsername , $dbPw);
dumpDb1('glx2022db',$dbHost, $dbUsername , $dbPw);

function dumpDb1($dbName,$dbHost, $dbUsername , $dbPw)
{
//1
    $file = ".\database\\$dbName.dump.".date('Y-m-d_H');
    if (file_exists($file)) {
        unlink($file);
    }

    $fileMt = ".\database\meta_data_save.".date('Y-m-d_H');
    $meta = file_get_contents('https://mytree.vn/tool1/admin/get_meta_table.php?get123=1');
    file_put_contents($fileMt, $meta);

    echo "\n FILE = $file";
    $cmd = "mysqldump -u root $dbName > " . $file;
    $cmd = "mysqldump -P 3306 -h $dbHost -u $dbUsername -p$dbPw $dbName --no-data --ignore-table=$dbName.rand_table   > " . $file;
    $cmd = "mysqldump -P 3306 -h $dbHost -u $dbUsername -p$dbPw $dbName --no-data > ". $file;
    echo "\n CMD = " . $cmd;
    exec($cmd);

    if (! file_exists($file) || ! trim(file_get_contents($file))) {
        exit("\n\n*** Error: can not dump db?");
    }
}


//system('php save_code.php');
