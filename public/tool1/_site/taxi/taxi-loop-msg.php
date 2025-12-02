<?php

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'taxi.mytree.vn';

error_reporting(E_ALL);
ini_set('display_errors', 1);

global $disableForm;

define("DEF_TOOL_CMS", 1);
//$_SERVER['SERVER_NAME'] = '';
require "/var/www/html/public/index.php";
require_once "lib_taxi.php";

function ol1($str)
{
    file_put_contents("/var/glx/weblog/taxi_2025.log", date("Y-m-d H:i:s") . "#" . $str . "\n", FILE_APPEND);
}

//  $message_ids_string

$mmMarkStopSendMessage = [];

$cc0 = 0;
while (1){
    if($cc0 > 0){
        echo "\n Sleep 1s before next loop...";
        sleep(1);
    }

    $cc0++;
    $mm = \App\Models\CrmAppInfo::where("ready", 1)->get();

    foreach ($mm as $item) {

        usleep(10000);

        $firebaseToken = $item->firebase_token;
        $request = json_decode($item->last_request, true);

        if (!$firebaseToken || !$request) {
            continue; // Skip if no token or request data
        }

        $viTri1 = $request['vi_tri1'] ?? '';
        $viTri2 = $request['vi_tri2'] ?? '';
        $phut = $request['phut'] ?? '';

        $allTin = searchTaxiMessages(
            $viTri1,
            $viTri2,
            $phut);

        if (!$allTin) {
            echo "❌ No messages found for: $viTri1 to $viTri2 in last $phut minutes\n";
            continue; // Skip if no messages found
        }
        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        print_r($mmMarkStopSendMessage);
        echo "</pre>";

        $message_ids_string = $allTin['message_ids_string'] ?? '';
        if($message_ids_string)
            $message_ids_string = "FBID=$item->id.$message_ids_string";

        echo "\n message_ids_string = $message_ids_string";
        echo "✅ Found " . count($allTin['messages']) . " messages for: $viTri1 to $viTri2 in last $phut minutes\n";
        if(!isset($mmMarkStopSendMessage[$message_ids_string])){
            //Tìm mọi phần tử trong  mảng $mmMarkStopSendMessage mà có key bắt đầu là $item->id, unset key này
            foreach ($mmMarkStopSendMessage as $key => $value) {
                if (strpos($key, "FBID=$item->id.") === 0) {
                    unset($mmMarkStopSendMessage[$key]);
                }
            }
            $mmMarkStopSendMessage[$message_ids_string] = 1;

        }
        else{
            $mmMarkStopSendMessage[$message_ids_string]++;
        }

        if($mmMarkStopSendMessage[$message_ids_string] > 1){
            echo "\n❌ Stop send message for: " . $mmMarkStopSendMessage[$message_ids_string] . " times\n";
            continue;
        }



        $accessToken = getAccessToken($SERVICE_ACCOUNT_FILE);
        echo "✅ Access token received\n\n";

        $urgentData = [
            "booking_id" => "IDF_" . time(),
            "pickup_location" => "Có Khách gọi chuyến của bạn",
            "destination" => "Test Destination",
            "priority" => "urgent"
        ];

        $response = sendNotificationV1(
            $PROJECT_ID,
            $accessToken,
            $firebaseToken,
            "🚨 ($item->id) Taxi Có chuyến mới",
            "Hãy vào App kiểm tra - " . date('H:i:s'),
            $urgentData
        );

        echo $response['success'] ? "✅ Notification Sent!" : "❌ Failed";
//        echo "\n\n Sleep 15s";

    }
}
