<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'v2.4share.vn';

require_once "/var/www/html/public/index.php";

if (!isCli()) {
    die("Not cli!");
}

$tl = getch("...xoa ?");
if($tl == 'y') {
    \App\Models\OrderInfo::truncate();
    \App\Models\OrderItem::truncate();
}
// Thông tin kết nối
$servername = "sv216230";
$username = "webuser02";
$password = env('DB_RM_PW1');
$dbname = "test2019";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$maxId = \App\Models\OrderItem::max('id');
$maxId1 = $maxId - 5;
//$maxId1 = 0;

die("Sync gold 41 ");
getch(" max id = $maxId");

// Thực hiện truy vấn
$sql = "SELECT * FROM cms_gold WHERE id > $maxId1 ORDER BY id ASC LIMIT 10000000 ";
//$sql = "SELECT * FROM tbl_payment ORDER BY id ASC LIMIT 10000000 ";

//$sql = "SELECT * FROM tbl_payment WHERE id > $maxId1 AND userid = 446231 ORDER BY id ASC LIMIT 10000000 ";

$uidListNotfound = '';

$result = $conn->query($sql);
$cc = 0;
$cc = 0;
$timeStart = time();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cc++;
        $obj = (object)$row;
        $speed = $cc / (time() - $timeStart + 1);
        echo "<br/>\n $cc/$result->num_rows .$obj->date, tbl_payment= $obj->id, price = $obj->price, UID = $obj->userid, ($speed / s)";

        if(!$obj->price){
            continue;
        }

        $date10S = nowyh(strtotime($obj->date) + 120);
//        $sql1 = "SELECT * FROM cms_gold WHERE bill_id = $obj->id LIMIT 1";
        $sql1 = "SELECT * FROM cms_gold WHERE userid = $obj->userid AND ((`date` >= '$obj->date' AND date <= '$date10S') OR (reason LIKE '%$obj->order_id%') ) LIMIT 1";
        $res1 = $conn->query($sql1);
        if(!$row1 = $res1->fetch_assoc()){

            $uidListNotfound .= $obj->userid.",";
//            getch("Not found ...");

            continue;
        }

        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        print_r($obj);
        echo "</pre>";

        getch("...");

        $obj1 = (object)$row1;



//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($obj1);
//        echo "</pre>";

        $nGold = $obj1->gold;
        $reason = $obj1->reason;



        try {
            $x0 = \App\Models\OrderInfo::find($obj->id);
//            $x = \App\Models\OrderInfo::withTrashed()->find($obj->id);
           //Nếu cả thùng rác và bảng chính đều không có thì mới insert
            $new = new \App\Models\OrderInfo();
            if (!$x0) {
                $new->id = $obj->id;
                $new->user_id = $obj->userid;
                $new->money = $obj->price;
                $new->created_at = $obj->date;
                $new->transaction_id_remote   = $obj->trans_id;
                $new->transaction_id_local  = $obj->order_id;
                $new->comment = $obj->info;
                $new->remote_ip = $obj->ip;

                //save là insert
                $new->save();
                echo "<br/>\n Insert ok";
            } else {
                echo "<br/>\n Đã tồn tại!";
                $new = $x0;
            }

            $new->reason = strip_tags($reason);
            $new->tmp_ngold = $nGold;

//            if($prod = \App\Models\Product::where("price", $new->money)->where("parent_id", 4)->first())
            $prod = null;
            \App\Http\Controllers\OrderItemController::addOrderItemFromOrder($new, $prod, null);


        } catch (Throwable $e) { // For PHP 7
            echo "<br/>\n Error1: " . $e->getMessage();
            echo "<br/>\n Error1: " . $e->getTraceAsString();
            getch("...");
        }
    }
} else {
    echo "0 results";
}

// Đóng kết nối
$conn->close();

