<?php
$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'taxi.mytree.vn';;
error_reporting(E_ALL);
ini_set('display_errors', 1);

define("DEF_TOOL_CMS", 1);
//$_SERVER['SERVER_NAME'] = '';
require "/var/www/html/public/index.php";
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
function ol1($str)
{
    global $ip;
    file_put_contents("/var/glx/weblog/taxi_2025.log", date("Y-m-d H:i:s") . " # $ip # " . $str . "\n", FILE_APPEND);
}

$fullUrl = \LadLib\Common\UrlHelper1::getFullUrl();
ol1("\n\n URLx = $fullUrl");
//ol1("\n\n+++ SVX=" . serialize($_SERVER));
ol1("\n\n--- SVX=" . serialize($_REQUEST));

$HTTP_X_FCM_TOKEN = $_SERVER['HTTP_X_FCM_TOKEN'] ?? '';
if (!$HTTP_X_FCM_TOKEN) {

    ol1("Not found HTTP_X_FCM_TOKEN");
    die("Not found HTTP_X_FCM_TOKEN");
}
else{
    ol1("HTTP_X_FCM_TOKEN = $HTTP_X_FCM_TOKEN");
}

$online = request('set_online', -1);

function styleStrStatus($str)
{
    $str = str_replace("\n", "<br/>", $str);
    return "
    <div style='padding: 5px; color: green'>

$str
</div>
    ";

}

try {

    $obj = \App\Models\CrmAppInfo::insertOrUpdateFBTokenAndReadyStatus(
        $HTTP_X_FCM_TOKEN,
        $online
        );

//die("xxx");
//    if ($online == 1 || ($online == -1) && $obj->ready)
    if(1)
    {

        $str = "";

        if($obj->alert_time){
            $obj->alert_time = trim($obj->alert_time);
//            echo("xxx $obj->id ");
//            return;
            if(!$obj->ready){
                $str.= "<div style='color: gray'> (Dừng báo chuông) </div>";
            }
            else
            if($jsobj = json_decode($obj->alert_time)){

//                die($obj->alert_time);
                if($jsobj->from1 != '...' && $jsobj->to1 != '...'){
                    if(strlen($jsobj->from1) ==5 && strlen($jsobj->to1) == 5 && strstr($jsobj->from1, ':') && strstr($jsobj->to1, ':'))
                        $str.= "- Giờ báo chuông: <b style='color:red'> $jsobj->from1 - $jsobj->to1 </b>  \r\n";
                }
                if($jsobj->from2 != '...' && $jsobj->to2 != '...'){
                    if(strlen($jsobj->from2) ==5 && strlen($jsobj->to2) == 5 && strstr($jsobj->from2, ':') && strstr($jsobj->to2, ':'))
                        $str.= "- Giờ báo chuông: <b style='color:red'> $jsobj->from2 - $jsobj->to2 </b>  \r\n";
                }
            }

        }
        if($obj->last_request){
            $rq = json_decode($obj->last_request, true);
            $rq = (object)$rq; // Convert to object for easier access
            if($rq->vi_tri1){
                $str.= "> Điểm đi: <b> $rq->vi_tri1 </b>  \r\n";
            }
            if($rq->vi_tri2){
                $str.= "> Điểm đến: <b> $rq->vi_tri2 </b> \r\n";
            }
            if($rq->phut){
                $str.= "> Tối đa<b>  $rq->phut phút </b>  gần nhất \r\n";
            }
        }
        if(!$str){
            $str = "Bạn Chưa chọn điểm đi/đến?";
        }
        else
            $str = " * Bạn đang tìm chuyến:\n$str\n <i>  </i>";

        $mm = [
            'done' => 1,
            'message' => styleStrStatus($str),
            'ready' => $obj->ready,
            'vi_tri1' => $rq->vi_tri1 ?? '',
            'vi_tri2' => $rq->vi_tri2 ?? '',
            'phut' => $rq->phut ?? '',
        ];
    } else {
        $mm = [
            'done' => 1,
            'message' => styleStrStatus('<div style="color: orange; display: flex; align-content: center; justify-content: center; align-items: center; height: 100px"> <b> Đã tắt Chuông báo tìm chuyến! </b> </div>'),
            'ready' => $obj->ready,

        ];
    }
    echo json_encode($mm, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (Throwable $e) { // For PHP 7
    ol1(" *** Error1: " . $e->getMessage());
}
