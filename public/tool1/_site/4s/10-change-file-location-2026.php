<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = '4share.vn';
require_once "/var/www/html/public/index.php";

$fid = $_REQUEST['fid'] ?? 0;
$oldLoc = trim($_REQUEST['old_location'] ?? '');
$newLoc = trim($_REQUEST['new_location'] ?? '');

if($fc = \App\Models\FileCloud::find($fid)){
    if($fc->location1 == $newLoc && $fc->server1 == 'sv18.4share.vn'){
        die("DONE_NEW_LOC 1");
    }

    $fc->location1 = $newLoc;
    $fc->addLog("Change Location: $oldLoc->$newLoc, $fc->server1 => 'sv18.4share.vn';");
    $fc->server1 = 'sv18.4share.vn';
    $fc->update();
    die("DONE_NEW_LOC 2 ");


}
die("NOTOK NotFoundInDB-$fid OldNew= $oldLoc / $newLoc");
