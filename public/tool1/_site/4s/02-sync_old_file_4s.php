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
    \App\Models\FileUpload::truncate();

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

$maxId = \App\Models\FileUpload::max('id');
$maxId1 = $maxId - 5;
$maxId1 = 0;

// Thực hiện truy vấn
$sql = "SELECT id, filepath, name, size, userid , checksum,  createdAt, idlink, mime, parent, server1, location1, ".
"delete_date, lastdate_down, download_his, delete_date_real , delete_date_real2, link1, count_down, md5, crc32b
FROM cloud_file WHERE id > $maxId1 AND userid > 0 ORDER BY id DESC LIMIT 10000000 ";
$result = $conn->query($sql);
$cc = 0;
$timeStart = time();
$ttDelete = 0;
$listErrorId = '';
if ($result->num_rows > 0) {
    // In kết quả
    while ($row = $result->fetch_assoc()) {
        $cc++;


        $obj = (object)$row;
        //echo "<br>id: " . $row["id"] . " - Name: " . $row["email"] . "<br>";
        $speed = $cc / (time() - $timeStart + 1);
        echo "<br/>\n $cc/$result->num_rows . $obj->id, ttDelete = $ttDelete  ($speed / s)";
//        if($cc < 1250350 - 10)
//            continue;
        //if(!\App\Models\User::where('email', $obj->email)->first()){
        try {
//            $x = \App\Models\FileUpload::find($obj->id);
            $x0 = \App\Models\FileUpload::find($obj->id);
            $x = \App\Models\FileUpload::withTrashed()->find($obj->id);
//            dump("x = " . $x);
//
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($x);
//            echo "</pre>";

            //Nếu cả thùng rác và bảng chính đều không có thì mới insert
            if (!$x && !$x0) {
                $new = new \App\Models\FileUpload();
                $new->id = $obj->id;
                $new->name = $obj->name;
                $new->link1 = $obj->link1;
                $new->mime = substr($obj->mime,0, 100);
                $new->user_id  = $obj->userid ;
                $new->file_size  = $obj->size ;
                $new->count_download = $obj->count_down;
                $new->cloud_id  = $new->idlink  = $obj->idlink ;
//                $new->server1  = $obj->server1 ;
//                $new->location1  = $obj->location1 ;
                $new->md5  = $obj->md5 ;
                $new->checksum  = $obj->checksum ;
                $new->crc32  = $obj->crc32b ;
                if($obj->delete_date_real )
                    $new->deleted_at  = nowyh(strtotime($obj->delete_date_real)) ;
                $new->parent_id   = $obj->parent ;
                if($obj->createdAt)
                    $new->created_at = nowyh(strtotime($obj->createdAt)) ;
//                if($new->deleted_at && $new->deleted_at[0] == '-')
//                    $new->deleted_at = null;
//                if($new->created_at && $new->created_at[0] == '-')
//                    $new->created_at = null;

                $new->file_path  = $obj->filepath;
                //save là insert
//                $new->save();
                echo "<br/>\n Check Insert ??";
//                getch("...");
            } else {

                if($x0){
                    if($obj->lastdate_down){
                        $old = $x0->lastdate_down;
                        $x0->lastdate_down = nowy(($obj->lastdate_down));
                        $x0->addLog("update  lastdate_down0 $old-> $obj->lastdate_down -> $x0->lastdate_down? ...");
                        $x0->save();
                    }
                    if($obj->download_his) {
                        $x0->download_his = $obj->download_his;
                        $x0->save();
                    }
                }
                if($x){
                    if($obj->lastdate_down){
                        $old = $x->lastdate_down;
                        $x->lastdate_down = nowy(($obj->lastdate_down));
                        $x->addLog("update  lastdate_down1 $old-> $obj->lastdate_down -> $x->lastdate_down? ...");
                        $x->save();
                    }
                    if($obj->download_his) {
                        $x->download_his = $obj->download_his;
                        $x->save();
                    }
                }

                if($obj->delete_date && $x0 && !$x0->deleted_at){

                    //Tìm xem có file nào có cloud_id trỏ đến file này không, nếu có thì không xóa
                    if($obj->idlink)
                    if(\App\Models\FileUpload::where("cloud_id", $obj->idlink)->count() > 1){
//                        getch(" Have id link $obj->idlink ");
                        echo "<br/>\n Đã tồn tại link đến file này!";
//                        continue;
                    }

                    if(!$x0->deleted_at){
                        $x0->deleted_at = $obj->delete_date;
                        $x0->addLog("update delete from old 4s");
                        echo "\n need đelete";
                        $x0->save();
                        $ttDelete++;
                    }
//                    getch(" update delete date? ...");
                }

                echo "<br/>\n Đã tồn tại!";
            }
        } catch (Throwable $e) { // For PHP 7
            $listErrorId .= $obj->id . ', ';
            echo "<br/>\n Error1: " . $e->getMessage();
            echo "<br/>\n Error1: " . $e->getTraceAsString();
//            getch("...");
        }
    }
} else {
    echo "0 results";
}

// Đóng kết nối
$conn->close();

echo "\n\n listErrorId = $listErrorId";
