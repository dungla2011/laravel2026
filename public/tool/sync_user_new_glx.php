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

$url = "https://galaxycloud.vn/tool/sync_user_glx_new.php";


$user = new \App\Models\User();

$ct = file_get_contents("https://galaxycloud.vn/tool/sync_user_glx_new.php");

$json = json_decode($ct);

//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($json);
//echo "</pre>";
foreach ($json AS $one){
//    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//    print_r($one);
//    echo "</pre>";
    $em = $one->email;
    $username = $one->username;
//    continue;
    echo "<br/>\n $em, $username";
    if(!$user = \App\Models\User::where("email", $em)->first()){

        \App\Models\User::addUserAndPassword($em, $username, time());

    }
    else{
        echo "<br/>\n Đã tồn tại!";;
        if(!$user->old_uid){
            $user->old_uid = $one->id;
            $user->update();
            echo "<br/>\n Update UID;";
        }
    }


}
