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
    \App\Models\FileCloud::truncate();

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

$maxId = \App\Models\FileCloud::max('id');
$maxId1 = $maxId - 5;
//$maxId1 = 0;

// Thực hiện truy vấn
$sql = "SELECT id, filepath, name, size, userid , checksum,  createdAt, idlink, mime, parent, server1, location1, ".
"delete_date_real , delete_date_real2, link1, count_down, md5, crc32b
FROM cloud_file WHERE id > $maxId1 ORDER BY id ASC LIMIT 10000000 ";
$result = $conn->query($sql);
$nError = $cc = 0;

$cc = 0;
$timeStart = time();

if ($result->num_rows > 0) {
    // In kết quả
    while ($row = $result->fetch_assoc()) {
        $cc++;

        $obj = (object)$row;
        //echo "<br>id: " . $row["id"] . " - Name: " . $row["email"] . "<br>";
        $speed = $cc / (time() - $timeStart + 1);
        echo "<br/>\n $cc/$result->num_rows . $obj->id,  ($speed / s)";
        echo "<br/>\n nError = $nError";

        if($obj->idlink)
            continue;
//        if($cc < 1250350 - 10)
//            continue;
        //if(!\App\Models\User::where('email', $obj->email)->first()){
        try {
//            $x = \App\Models\FileCloud::find($obj->id);
            $x0 = \App\Models\FileCloud::find($obj->id);
//            $x = \App\Models\FileCloud::withTrashed()->find($obj->id);

            //Nếu cả thùng rác và bảng chính đều không có thì mới insert
            if (!$x0) {
                $new = new \App\Models\FileCloud();
                $new->id = $obj->id;
//                $new->name = $obj->name;
                $new->name = '';
                $new->mime = $obj->mime;
                $new->user_id  = $obj->userid ;
                $new->size  = $obj->size ;
//                $new->count_download = $obj->count_down;
                $new->server1  = $obj->server1 ;
                $new->location1  = $obj->location1 ;
                $new->md5  = $obj->md5 ;
                $new->checksum  = $obj->checksum ;
                $new->crc32  = $obj->crc32b ;
                $new->delete_date_real  = substr($obj->delete_date_real,0,30);

//                if($obj->delete_date_real )
//                    $new->deleted_at  = nowyh(strtotime($obj->delete_date_real)) ;
//                if($obj->createdAt)
//                    $new->created_at = nowyh(strtotime($obj->createdAt)) ;


//                if($obj->delete_date_real && $obj->delete_date_real[0] == '-')
//                    $new->deleted_at = null;
                if($obj->createdAt)
                    if($obj->createdAt[0] == '-')
                        $new->created_at = null;
                    else
                        $new->created_at = $obj->createdAt;

//                $new->file_path  = $obj->filepath;
                //save là insert
                $new->save();
                echo "<br/>\n Insert ok";
            } else {
                echo "<br/>\n Đã tồn tại!";
            }
        } catch (Throwable $e) { // For PHP 7
            $nError++;
            echo "<br/>\n Error1: " . $e->getMessage();
            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
            print_r($e->getTraceAsString());
            echo "</pre>";
//            getch("...");
        }
    }
} else {
    echo "0 results";
}

// Đóng kết nối
$conn->close();

