<?php
//$time = microtime(1);
//use App\Models\User_Meta;
//
//$GLOBALS['DISABLE_DEBUG_BAR'] = 0;
error_reporting(E_ALL);
ini_set('display_errors', 1);

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'glx.com.vn';
use App\Helpers\BkavEHoaDonAPI;

//
require "/var/www/html/vendor/autoload.php";
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$mm = \App\Models\VpsUsage::all();

foreach ($mm AS $obj){

        try{


//    echo "<br/>\n $obj->price_config ";
    $tmp = str_replace(
         '{"n_cpu_core_price":50000,"n_ram_gb_price":30000,"n_gb_disk_price":1,"n_ip_address_price":50000,"n_network_dedicated_mbit_price":1000000}',
         '{"n_cpu_core_price":50000,"n_ram_gb_price":30000,"n_gb_disk_price":1000,"n_ip_address_price":50000,"n_network_dedicated_mbit_price":1000000}',
        $obj->price_config);

    if($tmp != $obj->price_config){
        echo "<br/>\n $tmp ";
        echo "<br/>\n ***** == ";
        $obj->price_config = $tmp;
        $obj->update();
    }

        }
        catch (Throwable $e) { // For PHP 7
            echo "<br/>\n Error1: ".$e->getMessage();
        }
        catch (Exception $exception){
            echo "<br/>\n Error2: ".$exception->getMessage();
        }

}
