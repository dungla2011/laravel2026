<?php


//$time = microtime(1);
//use App\Models\User_Meta;
//
//$GLOBALS['DISABLE_DEBUG_BAR'] = 0;
error_reporting(E_ALL);
ini_set('display_errors', 1);
//
//
require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

if(!isAdminCookie()){
    die("Not admin !");
}

$ret = file_get_contents('https://galaxycloud.vn/tool/sync_vm_price_glx.php?json=1');

$m1 = json_decode($ret);

echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
print_r($m1);
echo "</pre>";

$mm = \App\Models\VpsUsage::all();

foreach ($mm AS $one){
    echo "\n";
    if($price = ($m1->{$one->bios_uuid} ?? '')){
        echo "<br/>\n ";
        echo "\n FOUND $one->name, $one->bios_uuid, $price";

        if($price && $one->price_month != $price) {
            $one->price_month = $price;
            $one->update();
            echo "<br/>\n Update... $price";
        }

    }

}
