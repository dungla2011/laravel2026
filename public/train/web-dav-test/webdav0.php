<?php

/**
 * LAD 17.10.24
 * window map network driver
 * Đơn giản thế này có thể tạo được folder, upload được file OK!
 * Chú ý folder  cần có quyền Write
 *
 */

function ol3($str)
{
    $bname = basename(__FILE__);
    file_put_contents("/var/glx/weblog/$bname.log", $str . "\n", FILE_APPEND);
}

//$body =  substr(file_get_contents("php://input"), 0, 500);
//ol3("-_SERVER = ". serialize($_SERVER));
//ol3("-_REQUEST = ". serialize($_REQUEST));
//ol3("-body = ". serialize($body));


error_reporting(E_ALL);
ini_set('display_errors', 1);
require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle( $request = Illuminate\Http\Request::capture());
//////////////////////////////// Kết thúc đoạn Init Framework Laravel///
$user = \App\Models\User::find(1)->toArray();
$publicDir = '/share'; // WebDAV Bao loi
$publicDir = "/share/dav/";
//$publicDir = "/var/www/html/public/images"; // WebDAV  OK
//$publicDir = "/share-web-dav";
$server = new \Sabre\DAV\Server(new \Sabre\DAV\FS\Directory($publicDir));
$server->setBaseUri('/train/web-dav-test/webdav0.php');

// Add the browser plugin
$browser = new \Sabre\DAV\Browser\Plugin();
$server->addPlugin($browser);
$server->exec();

