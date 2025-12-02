<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$domainX = $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'v2.4share.vn';

require_once "/var/www/html/public/index.php";

if (!isCli()) {
    die("Not cli!");
}

echo "<br/>\n123";

// Thông tin kết nối
$servername = "sv216230";
$username = "webuser02";
$password = env('DB_RM_PW1');
$dbname = "test2019";


$dbName = $GLOBALS['mMapDomainDb'][$domainX]['db_name'];


// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$map = [
    'username' => 'username',
    'email' => 'email',
    'bank_info' => '',
    'registerDate' => "created_at",
];

$maxId = \App\Models\UserCloud::max('id');
$maxId1 = $maxId - 10;
//$maxId1 = 0;

// Thực hiện truy vấn
$sql = "SELECT * FROM cloud_user WHERE id > $maxId1 ORDER BY id ASC LIMIT 10000000 ";
$result = $conn->query($sql);
$cc = 0;
if ($result->num_rows > 0) {
    // In kết quả
    while ($row = $result->fetch_assoc()) {
        $cc++;
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($row);
//        echo "</pre>";
        $obj = (object)$row;
        //echo "<br>id: " . $row["id"] . " - Name: " . $row["email"] . "<br>";
        echo "<br/>\n $cc . $obj->id";
//        if($cc < 1250350 - 10)
//            continue;
        //if(!\App\Models\User::where('email', $obj->email)->first()){
        try {
            if (!($user = \App\Models\UserCloud::find($obj->id))) {
                $user = new \App\Models\UserCloud();
                $user->id = $obj->id;
                $user->user_id  = $obj->userid ;
                $user->quota_size = $obj->glx_bytes_in_avail;
                $user->quota_file = $obj->glx_files_in_avail;
                $user->glx_bytes_in_used = $obj->glx_bytes_in_used;
                $user->glx_files_in_used = $obj->glx_files_in_used;
                $user->location_store_file = $obj->glx_homedir;
                $user->quota_daily_download = $obj->quota_daily_download;
                $user->quota_limit_data = $obj->quota_limit_data;
                $user->glx_download_his = $obj->glx_download_his;

                $user->save();
//                $user->setUserTokenIfEmpty();
                echo "<br/>\n Insert ok";
            } else {
                echo "<br/>\n Đã tồn tại!";
            }
        } catch (Throwable $e) { // For PHP 7
            echo "<br/>\n Error1: " . $e->getMessage();
        } catch (Exception $exception) {
            echo "<br/>\n Error2: " . $exception->getMessage();
        }
    }
} else {
    echo "0 results";
}

// Đóng kết nối
$conn->close();

