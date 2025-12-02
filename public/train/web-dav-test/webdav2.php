<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/var/glx/weblog/test123.log');

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'cloud1.mytree.vn';

require '/var/www/html/vendor/autoload.php';
require 'lib-webdav.php';
$app = require_once '/var/www/html/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle( $request = Illuminate\Http\Request::capture());
//////////////////////////////// Kết thúc đoạn Init Framework Laravel///
$user = \App\Models\User::find(1)->toArray();

$publicDir = '/share'; // WebDAV Bao loi
$publicDir = "/var/www/html/public/images/tmp"; // WebDAV  OK
//$publicDir = "/"; // WebDAV  OK
//$publicDir = "/share-web-dav";
$server = new \Sabre\DAV\Server(new \Sabre\DAV\FS\Directory($publicDir));
//$server = new \Sabre\DAV\Server(new RemoteDirectory($publicDir, 1));
$server->setBaseUri('/train/web-dav-test/webdav2.php');
// Hàm callback để kiểm tra tên người dùng và mật khẩu
$callback = function($username, $password) {
    return $username === 'admin' && $password === '11112222';
};
$authBackend = new \Sabre\DAV\Auth\Backend\BasicCallBack($callback);
$authPlugin = new \Sabre\DAV\Auth\Plugin($authBackend, 'My Realm');
$server->addPlugin($authPlugin);

// Add the browser plugin
$browser = new \Sabre\DAV\Browser\Plugin();
$server->addPlugin($browser);
$server->exec();
