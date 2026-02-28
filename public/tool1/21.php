<?php
//$time = microtime(1);
//use App\Models\User_Meta;
//
//$GLOBALS['DISABLE_DEBUG_BAR'] = 0;
error_reporting(E_ALL);
ini_set('display_errors', 1);

use App\Helpers\BkavEHoaDonAPI;

//
require "/var/www/html/vendor/autoload.php";
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);


echo "<br/>\n111";

$mm = \App\Models\VpsUsage::all();

foreach ($mm as $item) {
//    echo "<br/><br>\n ($item->id) $item->name , $item->lastest_time_the_same";
    if($item->lastest_time_the_same < nowyh(time() - 3600 * 24)){
        if(!$item->end_time_used){
            echo "<br/>\n -- need fix ($item->id) $item->name , $item->lastest_time_the_same,x=$item->power_state";
            $item->end_time_used = $item->lastest_time_the_same;
            $item->update();
        }
    }
//    if($m1 = \App\Models\VpsUsage::where("bios_uuid", "$item->bios_uuid")->get())
//    foreach ($m1 AS $i1){
//        if($i1->id != $item->id){
//            echo "<br/>\n -- $i1->id | $item->name | $item->created_at ($item->bios_uuid)";
//        }
//    }
}
