<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$domainX = $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'v2.4share.vn';

require_once "/var/www/html/public/index.php";

if (!isCli()) {
    die("Not cli!");
}

$tl = getch("...xoa ?");
if($tl == 'y')
    \App\Models\User::truncate();


echo "<br/>\n123";

// Thông tin kết nối
$servername = "sv216230";
$username = "webuser02";
$password = env('DB_RM_PW1');
$dbname = "test2019";


$dbName = $GLOBALS['mMapDomainDb'][$domainX]['db_name'];

if(str_starts_with($dbName, 'DB_RM_HOST-')){

    $num = explode('-', $dbName)[1];
    $hostname2 = env('DB_RM_HOST' . $num);
    $dbname2 = $dbName = env('DB_RM_NAME' . $num);
    $user2 = env('DB_RM_USER' . $num);
    $pw2 = env('DB_RM_PW' . $num);

//    echo "<br/>\n $hostname2, $dbname2, $user2, $pw2";

    // Tạo kết nối
    $conn2 = new mysqli($hostname2, $user2, $pw2, $dbname2);

// Kiểm tra kết nối
    if ($conn2->connect_error) {
        die("Connection failed: " . $conn2->connect_error);
    }

}

function updatePw($id, $pw)
{
    global $conn2;
// Cập nhật dữ liệu
    $sql = "UPDATE users SET password='$pw' WHERE id=$id";

    if ($conn2->query($sql) === TRUE) {
        echo "Record updated successfully";
    } else {
        loi("Error updating record: " . $conn2->error);
    }

    echo("\n ...updated : $id  ");
//    getch(" ...updated : $id  ");
}


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

$maxId = \App\Models\User::max('id');
$maxId1 = $maxId - 10;
//$maxId1 = 0;

// Thực hiện truy vấn
$sql = "SELECT id, email, password, username, registerDate FROM cms_user WHERE id > $maxId1 ORDER BY id ASC LIMIT 10000000 ";
//$sql = "SELECT id, email, password, username, registerDate FROM cms_user WHERE id = 1646741 ORDER BY id ASC LIMIT 10000000 ";
$result = $conn->query($sql);
$cc = 0;
if ($result->num_rows > 0) {
    // In kết quả
    while ($row = $result->fetch_assoc()) {

        $cc++;
//
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($row);
//        echo "</pre>";
        $obj = (object)$row;
        //echo "<br>id: " . $row["id"] . " - Name: " . $row["email"] . "<br>";
        echo "<br/>\n $cc . $obj->id/$result->num_rows , $obj->email";
//        if($cc < 1250350 - 10)
//            continue;
        //if(!\App\Models\User::where('email', $obj->email)->first()){
        try {
            if ( ! ($user = \App\Models\User::find($obj->id))) {
                $user = new \App\Models\User();
                $user->id = $obj->id;
                $user->username = $obj->username;
                $user->email = $obj->email;

                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
                print_r($user->toArray());
                echo "</pre>";

                $date = nowyh(strtotime($obj->registerDate));
                if($date[0] == '_')
                    $user->created_at = null;
                else
                    $user->created_at = $date;

                $user->save();
                $user->setUserTokenIfEmpty();
                echo "<br/>\n Insert ok";
                $user->setRoleUserIfRoleNull();

                \App\Models\UserCloud::getOrCreateNewUserCloud($user->id);


            } else {
                if(!$user->token_user){
                    $user->token_user = $user->setUserTokenIfEmpty();
                }
//
//                $user->setRoleUserIfRoleNull();
//
//                updatePw($obj->id, $obj->password);

                echo "<br/>\n Đã tồn tại!";
            }
        } catch (Throwable $e) { // For PHP 7
            echo "<br/>\n Error1: " . $e->getMessage();
            echo "<br/>\n Error1: " . $e->getTraceAsString();
            getch(" ...Error1: ");
        } catch (Exception $exception) {

        }
    }
} else {
    echo "0 results";
}

// Đóng kết nối
$conn->close();

