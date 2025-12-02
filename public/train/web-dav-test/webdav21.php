<?php

// Your protected content goes here
//echo 'You are authenticated!';
error_reporting(E_ALL & ~E_STRICT);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/var/glx/weblog/error_webdav.log');
set_time_limit(1800);
function ol3($str)
{
    $bname = basename(__FILE__);
    file_put_contents("/var/glx/weblog/$bname.log", date("Y-m-d H:i:s") ." # ". $str . "\n", FILE_APPEND);
}

try {


$sr = serialize($_SERVER);
//if(str_contains($sr, 'Zscaler'))
//    ol3(" \n\n SERVER = " . serialize($_SERVER) . "\n\n");

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'cloud1.mytree.vn';
//$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'test2023.mytree.vn';

$bname = basename(__FILE__);

require '/var/www/html/vendor/autoload.php';
require 'lib-webdav.php';
$app = require_once '/var/www/html/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle( $request = Illuminate\Http\Request::capture());
//////////////////////////////// Kết thúc đoạn Init Framework Laravel///

// Call the authenticate function at the start of your script
$uid = authenticateWebDav();




$publicDir = '/share'; // WebDAV Bao loi
$publicDir = "/var/www/html/public/images/tmp"; // WebDAV  OK
$publicDir = "/"; // WebDAV  OK
//$publicDir = "/share-web-dav";
//$server = new \Sabre\DAV\Server(new \Sabre\DAV\FS\Directory($publicDir));
//$server = new \Sabre\DAV\Server(new RemoteDirectory($publicDir, 1));

$siteId = \App\Models\SiteMng::getSiteId();
$pathRoot = "/share/dav/siteid_$siteId/$uid";

if(!file_exists($pathRoot))
    mkdir($pathRoot, 0777, true);

$dir = new RemoteDirectory($publicDir, $pathRoot, $uid);

$server = new Server2($dir);
$server->pathRoot = $pathRoot;
$server->uid = $uid;
$server->siteId = $siteId;
$server->tree->uid = $uid;
$server->tree->server = $server;

$uri = \LadLib\Common\UrlHelper1::getUriWithoutParam();

$server->setBaseUri('/train/web-dav-test/' . $bname);
$server->setBaseUri('/tool/zcloud.io.vn');


// Add the browser plugin
$browser = new \Sabre\DAV\Browser\Plugin();

if($uid == 1)
    $server->addPlugin($browser);

$server->addPlugin(new CustomPutPlugin($server));
$server->addPlugin(new CapturePutPlugin($server));




// Neu ko co cai nay, se bao loi ra Log, du moi thu van chay bt
$lockBackend = new \Sabre\DAV\Locks\Backend\File('/share/dav/locks');
$lockPlugin = new \Sabre\DAV\Locks\Plugin($lockBackend);
$server->addPlugin($lockPlugin);

//$maxFileSize = 1 * 1024 * 1024; // 100MB
//$uploadLimitPlugin = new UploadLimitPlugin($maxFileSize);
//$server->addPlugin($uploadLimitPlugin);

// Enable detailed logging for SabreDAV
//if(0)
$server->on('beforeMethod', function($request, $response) {
    ol3(" \n\n Request1 getMethod = " . $request->getMethod() . "\n\n");
//    echo "<br/>\nRequest: " . $request->getMethod() . " " . $request->getUrl();
});
//if(0)
//Nếu để Throwable, có thể sẽ ko tạo được folder??? có vẻ ko đúng
$server->on('exception', function(Throwable $e) use ($uid) {
//$server->on('exception', function(Exception $e) {
    ob_clean();
    $msg = $e->getMessage() ?? ' NO_MESS';
    $trace0 = $e->getTraceAsString() ?? ' NO_TRACE';
    $trace = str_replace("\n", "===", $trace0);
    ol3("\n\n *** Error: $msg \n  $trace \n\n");
    if(isChromeUserAgent( $_SERVER['HTTP_USER_AGENT'])) {
        if(str_contains($msg, 'There was no plugin in the system that was willing to handle this GET method. Enable the Browser plugin to get a better result here'))
            echo "Exception Zcloud.io.vn: Not valid access, no Plug";
        else
            echo "ExceptionGlx: $msg";
        echo "\n";
        if($uid == 1)
            print_r($trace0);
        die();
    }

    //De DIE o day se co loi: không tạo được folder,  hoặc ko up được file

});

$server->exec();

}
catch (Exception $e) {
    ol3(" \n\n ====== Error: " . $e->getMessage() . "\n\n");
}
