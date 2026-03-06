<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);


require '/var/www/html/vendor/autoload.php';
$app = require_once  '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$helper = new \App\Helpers\ZaloHelper('http://localhost:30000', 'admin', '938475wufo87908u09');

$us = $helper->findUserByPhone('event1', "0869834392");

echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
print_r($us);
echo "</pre>";

return;

// Tìm user
$phone = "0902066768";
$phone = "0966616368";
$user = $helper->findUser('event1', $phone);
echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
print_r($user);
echo "</pre>";
if ($user['success']) {
    echo "<br>UID: " . $user['user']['uid'];
    echo "<br>Name: " . $user['user']['displayName'];
} else {
    echo "❌ Lỗi: " . $user['error'];
}

//return;
echo "<br/>\n";
$result = $helper->sendMessage('event1', '5138831621894942631', 'Hello12345!');


// Kiểm tra kết quả
if ($result['success'] ?? '') {
    echo "✅ Gửi thành công! msgId: " . $result['data']['msgId'];
} else {
    echo "❌ Lỗi: " . $result['error'];
}



