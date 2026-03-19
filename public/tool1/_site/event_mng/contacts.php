<?php
//$time = microtime(1);
//use App\Models\User_Meta;
//
//$GLOBALS['DISABLE_DEBUG_BAR'] = 0;
use App\Models\EventUserInfo;

error_reporting(E_ALL);
ini_set('display_errors', 1);
//
//
require __DIR__.'/../../../../vendor/autoload.php';
$app = require_once __DIR__.'/../../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$ret = [];
$mm = \App\Models\EventUserInfo::select([ 'id', 'last_name', 'first_name', 'phone'])->limit(1000000)->get();

$limit = request("limit");
$cc = 0;
foreach ($mm AS $obj){

    if($obj instanceof \App\Models\EventUserInfo);
    $fn = $obj->getFullname();

    $phone = EventUserInfo::fixPhoneNumber($obj->phone);

    if(!$phone)
        continue;


    if($obj->phone){

    }
    else
        $phone='no_phone';

    $ret[] = ['name' => "$obj->id.$fn.$phone.planed", 'phone'=>$phone];
    $cc++;
    if($limit && $cc > $limit)
        break;

//    echo "<br/>\n $fn.$phone.$obj->id";
}

ob_clean();
echo json_encode($ret, JSON_PRETTY_PRINT);
//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($ret);
//echo "</pre>";
