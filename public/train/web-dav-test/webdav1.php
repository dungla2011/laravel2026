<?php
set_time_limit(1800);
error_reporting(E_ALL);
ini_set('display_errors', 1);
require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle( $request = Illuminate\Http\Request::capture());
//////////////////////////////// Kết thúc đoạn Init Framework Laravel///
$user = \App\Models\User::find(1)->toArray();
$publicDir = '/share'; // WebDAV Bao loi
//$publicDir = "/var/www/html/public/images"; // WebDAV  OK
//$publicDir = "/share-web-dav";
$server = new \Sabre\DAV\Server(new \Sabre\DAV\FS\Directory($publicDir));
$server->setBaseUri('/train/web-dav-test/webdav1.php');
// Hàm callback để kiểm tra tên người dùng và mật khẩu
$callback = function($username, $password) {
    return $username === 'admin' && $password === '111111';
};
// Tạo một backend xác thực mới với hàm callback
$authBackend = new \Sabre\DAV\Auth\Backend\BasicCallBack($callback);
// Tạo một plugin xác thực mới với backend đã tạo
//$authPlugin = new \Sabre\DAV\Auth\Plugin($authBackend, 'My Realm');
// Thêm plugin xác thực vào máy chủ
//$server->addPlugin($authPlugin);
// Add the browser plugin
$browser = new \Sabre\DAV\Browser\Plugin();
$server->addPlugin($browser);
$server->exec();
