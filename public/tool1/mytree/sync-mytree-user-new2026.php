
<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
define("DEF_TOOL_CMS", 1);

require_once "/var/www/html/public/index.php";


if(!isSupperAdmin_()){
   die("ABC = not adm");
}


$pid = 11221377383137280; // ID của người dùng cần lấy cây gia phả
if($_GET['pid'] ?? ''){
    $pid = (int)$_GET['pid'];
}

//Lấy user_id của người dùng này
if($gp = \App\Models\GiaPha::find($pid)){
    $uid = $gp->user_id;
    //Lấy token user
    $tk = \App\Models\User::getTokenByUserId($uid);
}
else{
    die("Not valid pid: $pid" . PHP_EOL .
        "Please check if the user exists or if the pid is correct." . PHP_EOL);
}

$link = "https://v5.mytree.vn/api/member-tree-mng/tree?pid=$pid&get_tree_all=1&return_js=1";

//Curl access link by token (Bearer)

$timeStart = microtime(true);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $link);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $tk,
]);
$response = curl_exec($ch);
curl_close($ch);
$response = json_decode($response, true);
if (isset($response['error'])) {
    echo "Lỗi: " . $response['error'];
} else {

    echo "<br/>\n DTIME = ". (microtime(true) - $timeStart) . " giây<br/>\n";

    // In ra cây gia phả
    echo "<pre>";
    print_r($response);
    echo "</pre>";
}


