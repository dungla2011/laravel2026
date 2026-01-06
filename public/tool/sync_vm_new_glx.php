<?php
//$time = microtime(1);
//use App\Models\User_Meta;
//
//$GLOBALS['DISABLE_DEBUG_BAR'] = 0;
error_reporting(E_ALL);
ini_set('display_errors', 1);
//
//
$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'glx.lad.vn';

require __DIR__.'/../../vendor/autoload.php';
$app = require_once __DIR__.'/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$url = "https://galaxycloud.vn/tool/sync_vm_glx_new.php";


$obj = new \App\Models\VpsInstance();

$ct = file_get_contents("$url");

$json = json_decode($ct);

//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($json);
//echo "</pre>";
foreach ($json AS $one){
//    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//    print_r($one);
//    echo "</pre>";
    $vmName = $one->vmName;
    $vmId = $one->vmId;
    $uidOld = $one->userid;

    $bios_uuid = $one->bios_uuid;

    echo "<br/>\n $vmName, $vmId, $one->userid";


    if($uidOld)
    if($found = \App\Models\VpsInstance::where("bios_uuid", $bios_uuid)->where("name", $vmName)->first()){
        if(!$found->user_id){

           if($us =  \App\Models\User::where("old_uid", $uidOld)->first()){


               $found->user_id = $us->id;
               $found->update();

               echo "<br/>\n Update UID";
           }
        }

    }



//    $em = $one->email;
//    $username = $one->username;
//    continue;
//    echo "<br/>\n $em, $username";
//    if(!$user = \App\Models\User::where("email", $em)->first()){
//
//        \App\Models\User::addUserAndPassword($em, $username, time());
//
//    }
//    else{
//        echo "<br/>\n Đã tồn tại!";;
//    }


}
