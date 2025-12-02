<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'v2.4share.vn';

require_once "/var/www/html/public/index.php";

if (!isCli()) {
    die("Not cli!");
}

$tl = getch("...xoa ?");
if($tl == 'y')
    \App\Models\FolderFile::truncate();

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

$map = [
    'username' => 'username',
    'email' => 'email',
    'bank_info' => '',
    'registerDate' => "created_at",
];

$maxId = \App\Models\FolderFile::max('id');
$maxId1 = $maxId - 5;
$maxId1 = 0;

// Thực hiện truy vấn
$sql = "SELECT id, name, link1 ,createdAt, delete_date, userid, parent
FROM cloud_folder WHERE id > $maxId1 ORDER BY id DESC LIMIT 10000000 ";
$result = $conn->query($sql);
$cc = 0;$timeStart = time();
$needUpdateLink1 = 0;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cc++;
        $speed = $cc / (time() - $timeStart + 1);

        $obj = (object)$row;
        echo "<br/>\n $cc/$result->num_rows .needUpdateLink1 = $needUpdateLink1,  ID = $obj->id ($speed / s)";

        if($obj->name == '_Dữ liệu Không xóa')
            continue;

        try {
            if($x0 = \App\Models\FolderFile::find($obj->id)){
                if(!$x0->link1){
                    $x0->link1 = $obj->link1;
                    $needUpdateLink1++;
//                    $x0->save();
                }
            }
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

