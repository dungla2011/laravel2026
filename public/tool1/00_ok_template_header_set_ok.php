<?php
//Đoạn code chạy độc lập không dùng index.php
//Mẫu Như sau thì ko bị báo lỗi header, nếu ko thì bị báo lỗi header sent, không rõ tại sao...
error_reporting(E_ALL);
ini_set('display_errors', 1);



require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Kết thúc đoạn Init Framework Laravel
////////////////////////////////////////////////////////////////////


$user = \App\Models\User::find(1)->toArray();

$uid = getCurrentUserId();

echo "<br/>\nUID = $uid";

echo "<br>";
print_r($user['email']);

echo "<br/>\n";


header('Content-Type: text/html');
header('Content-Length: 1111');
echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
print_r(headers_list());
echo "</pre>";

$rq = request()->all();

echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
print_r($rq);
echo "</pre>";

echo "ABC";
