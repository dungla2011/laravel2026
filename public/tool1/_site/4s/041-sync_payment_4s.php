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

// Thực hiện truy vấn
//$sql = "SELECT * FROM tbl_payment WHERE id > $maxId1 ORDER BY id ASC LIMIT 10000000 ";
$sql = "SELECT * FROM cms_gold  WHERE id > $maxId1 ORDER BY id ASC LIMIT 10000000 ";

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
        echo "<br/>\n $cc/$result->num_rows .$obj->date, tbl_payment= $obj->id,  UID = $obj->userid, ($speed / s)";

        if($cc < 796000){
//            continue;
        }

//        if(!$obj->price){
//            continue;
//        }
        try {
            $x0 = \App\Models\OrderItem::find($obj->id);
           //Nếu cả thùng rác và bảng chính đều không có thì mới insert
            $new = new \App\Models\OrderItem();
            if (!$x0) {

                $price = 0;
                if($obj->bill_id){
                    //Lay ra tbl_payment co id = $obj->bill_id
                    $sql1 = "SELECT * FROM cms_bill_center WHERE id = '$obj->bill_id' AND userid = $obj->userid  LIMIT 1";
                    $res1 = $conn->query($sql1);
                    if($row1 = $res1->fetch_assoc()) {
                        $obj1 = (object)$row1;
                        $price = $obj1->price;
                    }
                    if(!$price){
                        $sql1 = "SELECT * FROM tbl_payment WHERE id = '$obj->bill_id' AND userid = $obj->userid LIMIT 1";
                        $res1 = $conn->query($sql1);
                        if($row1 = $res1->fetch_assoc()) {
                            $obj1 = (object)$row1;
                            $price = $obj1->price;
                        }
                    }
                }

                $new->id = $obj->id;
                $new->user_id = $obj->userid;
                if($price)
                    $new->price = $price;

                $new->created_at = $obj->date;
//                $new->transaction_id_remote   = $obj->trans_id;
//                $new->transaction_id_local  = $obj->order_id;
                $new->note = strip_tags($obj->reason);
                $decodedText = html_entity_decode($new->note, ENT_QUOTES, 'UTF-8');
                $new->note = $decodedText;

//                $new->remote_ip = $obj->ip;


                $new->tmp_ngold = $obj->gold;
                $new->tmp_gold_type = $obj->gold_type;
                if($obj->gold_type =='card')
                    $new->tmp_gold_type = 5;
                $new->tmp_json_old = json_encode($obj);

                //save là insert
                $new->save();
                echo "<br/>\n Insert ok";
            } else {
                echo "<br/>\n Đã tồn tại!";
//                $new = $x0;
            }

//            $new->reason = strip_tags($reason);


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

