<?php


error_reporting(E_ALL);
ini_set('display_errors', 1);
 //require_once "/var/www/html/public/index.php";
require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$domain = \LadLib\Common\UrlHelper1::getDomainHostName();



$free = disk_free_space("/");

$free2 = disk_free_space("/mnt/glx");

$freeUp = disk_free_space("/var/ufile");


if(strstr($domain, '4share.vn') !== false)
if($freeUp < 100*_GB){
    die("free_not_ok Free Up small: " . ByteSize($freeUp));
}
echo "<br/>\n FreeUp: "  . ByteSize($freeUp);

if($free2 < _GB){
    die("Free Cache small: " . ByteSize($free2)) . "/total:". ByteSize(disk_free_space('/mnt/glx'));
    echo "<br/>\n";
    echo "\n FREE disk: ". ByteSize($free);
    return;
}

echo "\n FREE all: ". ByteSize($free);
if(is_numeric($free))
if($free > 10 * _GB){
    echo "-free_ok_now:".ByteSize($free) . " , CacheFree: " . ByteSize($free2)." /total:". ByteSize(disk_free_space('/mnt/glx'));
}
else{
    echo "-free_not_ok:".ByteSize($free);
}
//
////////////////////////////////////////////////////
//$freeBak = CDisk::getFreeDiskInFilePath("/mnt/sdd");
//echo "\n<br> FREE BAK /mnt/sdd : ". ByteSize($freeBak);
//if($freeBak < 50 * _GB){
//    echo "-free_not_ok:".ByteSize($freeBak);
//}
