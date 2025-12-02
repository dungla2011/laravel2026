<?php
/**
 * có lúc lỗi dữ liệu phải lấy từ db cũ ra...
 * Không hiểu sao bản 3.3.2023, gia phả gia đình bị cắt mất các thành viên hàng cuối, phải lấy từ 28.2 về
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../index.php';
$mysqli = new mysqli('localhost', 'admin', 'Cloud...');
// Check connection
if ($mysqli->connect_errno) {
    echo 'Failed to connect to MySQL: '.$mysqli->connect_error;
    exit();
}

$dbtable = 'gia_phas';
$dbname = 'glxdb_test';
//$dbname = 'glx2022db';
$sql = "SELECT * FROM  $dbname.$dbtable  WHERE user_id =  16";
$result = mysqli_query($mysqli, $sql);
if (! $result) {
    exit("Error db: $sql/".mysqli_error());
}
$nRow = mysqli_num_rows($result);
echo "<br/> nRow = $nRow";

$count = 0;
$m1 = [];
$mx1 = [];
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $count++;
    $id = $row['id'];
    $mx1[] = $row;
    $m1[] = $id;
    echo "<br> $count/$nRow. $id";
    if ($count > 0) {

    }
}

$dbtable = 'gia_phas';
$dbname = 'glx2022db';
$sql = "SELECT * FROM  $dbname.$dbtable  WHERE user_id =  16";
$result = mysqli_query($mysqli, $sql);
if (! $result) {
    exit("Error db: $sql/".mysqli_error());
}
$nRow = mysqli_num_rows($result);
echo "<br/> nRow = $nRow";

$count = 0;
$m2 = [];
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $count++;
    $id = $row['id'];
    $m2[] = $id;
    echo "<br> $count/$nRow. $id";
    if ($count > 0) {

    }
}

$m3 = array_diff($m1, $m2);

echo '<pre> >>> '.__FILE__.'('.__LINE__.')<br/>';
print_r($m3);
echo '</pre>';

$cc = 0;
foreach ($mx1 as $obj) {
    foreach ($m3 as $tmp => $id) {
        if ($obj['id'] == $id) {
            $cc++;
            echo "<br/>\n $cc. ".$obj['id'].' . '.$obj['name'];
            //            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
            //            print_r($obj);
            //            echo "</pre>";
            //            \App\Models\GiaPha::insert($obj);
        }
    }
}
