<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = '4share.vn';

require_once "/var/www/html/public/index.php";

if (!isCli()) {
    die("Not cli!");
}


// Thông tin kết nối
$servername = "12.0.0.54";
$username =  env('DB_USERNAME_DEFAULT');
$password = env('DB_PASSWORD_DEFAULT');
$dbname = "glx_1kdl";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$priceAndPid = [
//    200000 => 201,
//    100000 => 200,
//    50000 => 199,
//    10000 => 198,
    20000 => 202,
];

$maxId = \App\Models\FileUpload::max('id');
$maxId1 = $maxId - 5;
//$maxId1 = 0;

// Thực hiện truy vấn
$sql = "SELECT order_infos.*, users.email FROM order_infos LEFT JOIN users ON order_infos.user_id = users.id WHERE order_infos.money=20000 ";
$result = $conn->query($sql);
$cc = 0;
$timeStart = time();

$listErrorId = '';
if ($result->num_rows > 0) {
    // In kết quả
    while ($row = $result->fetch_assoc()) {
        $cc++;

        $obj = (object)$row;
        //echo "<br>id: " . $row["id"] . " - Name: " . $row["email"] . "<br>";
        $speed = $cc / (time() - $timeStart + 1);
        echo "<br/>\n $cc/$result->num_rows . $obj->id, $obj->email, $obj->money  ($speed / s)";
//        if($cc < 1250350 - 10)
//            continue;
        if($us = \App\Models\User::where('email', $obj->email)->first()){
            echo "\n Found UID = $us->id ";
        }
        //Tao user new
        else{
            if(!$obj->email)
                continue;
            $uname = str_replace('@', '_', $obj->email);
            $us = new \App\Models\User();
            $us->email = $obj->email;
            $us->username = $uname;
//            $us->password = bcrypt('123456');
            $us->created_at = $obj->created_at;
            $us->addLog("Create new user from 1k.download");
            $us->save();
            $us->setRoleUserIfRoleNull();
            \App\Models\UserCloud::getOrCreateNewUserCloud($us->id);
            echo "\n New UID = $us->id ";
//            getch('Enter to continue...');
        }

        if(!isset($priceAndPid[$obj->money])){
            continue;
        }

        $prodId = $priceAndPid[$obj->money];

        $prod = \App\Models\Product::find($prodId);
        if(!$prod){
            $listErrorId .= $obj->id . ',';
            continue;
        }

        if(\App\Models\OrderItem::where("user_id", $us->id)->where('price', $obj->money)->where("created_at", $obj->created_at)->first()){
            echo "\n Order existed!";
//            getch("...");
            continue;
        }

//        getch('Enter to continue...');
        $orderStd = new \stdClass();
//                    $orderStd->id = $orderId;
        $orderStd->user_id = $us->id;
        $orderStd->transaction_id_local = $obj->transaction_id_local;
        $orderStd->money = intval($obj->money);
        $orderStd->transaction_id_remote = $obj->transaction_id_remote;
        $orderStd->created_at = $obj->created_at;

        \App\Http\Controllers\OrderItemController::addOrderItemFromOrder($orderStd, $prod, 'sync_from_1k.dl', clsBaoKim::$name);

//        getch('Enter to continue...');




    }
} else {
    echo "0 results";
}

// Đóng kết nối
$conn->close();

echo "\n\n listErrorId = $listErrorId";
