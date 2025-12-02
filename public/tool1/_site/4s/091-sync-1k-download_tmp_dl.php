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

// Thực hiện truy vấn
$sql = "SELECT download_logs.*, users.email FROM download_logs LEFT JOIN users ON download_logs.user_id = users.id";
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
        echo "<br/>\n $cc/$result->num_rows . LOGID=$obj->id, $obj->email, $obj->file_id ,($speed / s)";
//        if($cc < 1250350 - 10)
//            continue;
        if(!$us = \App\Models\User::where('email', $obj->email)->first()){
            continue;
        }
        echo "\n Found UID = $us->id ";

        if(\App\Models\TmpDownloadSession::where("user_id", $us->id)->where('fid', $obj->file_id)->where("created_at", $obj->created_at)->first()){
            echo "\n TmpDownloadSession existed!";
//            getch("...");
            continue;
        }

//        getch("...");

        $newLog = new \App\Models\TmpDownloadSession();
        $newLog->user_id = $us->id;
        $newLog->fid = $obj->file_id;
        $size = \App\Models\FileUpload::find($obj->file_id)?->file_size;
        if(!$size)
            $size = \App\Models\FileUpload::withTrashed()->find($obj->file_id)?->file_size;

        $newLog->file_size = $size;
        $newLog->done_bytes = $size;
        $newLog->created_at = $obj->created_at;
        $newLog->save();




    }
} else {
    echo "0 results";
}

// Đóng kết nối
$conn->close();

echo "\n\n listErrorId = $listErrorId";
