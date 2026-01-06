<?php
//$time = microtime(1);
//use App\Models\User_Meta;
//
//$GLOBALS['DISABLE_DEBUG_BAR'] = 0;
error_reporting(E_ALL);
ini_set('display_errors', 1);
//
//
require __DIR__.'/../../vendor/autoload.php';
$app = require_once __DIR__.'/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);




$helper = new ZaloHelper('http://localhost:30000', 'admin', '938475wufo87908u09');


// Tìm user
$user = $helper->findUser('event1', '0902066768');
echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
print_r($user);
echo "</pre>";
if ($user['success']) {
    echo "UID: " . $user['user']['uid'];
    echo "Name: " . $user['user']['displayName'];
}
else{
    echo "❌ Lỗi: " . $user['error'];
}

return;

$result = $helper->sendMessage('event1', '7714268566922297972', 'Hello!');


// Kiểm tra kết quả
if ($result['success'] ?? '') {
    echo "✅ Gửi thành công! msgId: " . $result['data']['msgId'];
} else {
    echo "❌ Lỗi: " . $result['error'];
}


?>
