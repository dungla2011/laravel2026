<?php

//$time = microtime(1);
//use App\Models\User_Meta;
//
//$GLOBALS['DISABLE_DEBUG_BAR'] = 0;
error_reporting(E_ALL);
ini_set('display_errors', 1);
//
$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = '4share.vn';

//
require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$file = "/share/sv18/sv18-2025-12-03.OK.log";
$needDeleteList = "/share/sv18/need_delete.log";
if(file_exists($needDeleteList))
    unlink($needDeleteList);

$mm = file($file);
$cc = 0;
$nNotFound = 0;
$nNotFoundSize = 0;
$nNeedDelete = 0;
$nNeedDeleteSize = 0;
$nDiffSize = 0;
$nDiffSizeByte  = 0;
$totalSize = 0;

foreach ($mm AS $line){
    $line = trim($line);
    $cc++;
    echo "\n\n $cc . $line";

    list($path, $size, $cs) = explode("|", $line);
    $totalSize += $size;

    $fid = basename($path);

    echo "\n $fid, $path, $size, $cs";

    if($obj = \App\Models\FileCloud::find($fid)){
        if($obj->delete_date_real) {
            $nNeedDelete++;
            $nNeedDeleteSize+=$size;
            output($needDeleteList, $fid);
        }
        if($obj->size != $size){
            $nDiffSize++;
            $nDiffSizeByte+=$size;
        }
    }
    else{
        $nNotFound++;
        $nNotFoundSize+= $size;
    }
    echo "\n Not found: $nNotFound, Size = ". ByteSize($nNotFoundSize);
    echo "\n Need delete: $nNeedDelete, Size = ". ByteSize($nNeedDeleteSize);
    echo "\n Diff Size: $nDiffSize, Size = ". ByteSize($nDiffSizeByte);
    echo "\n Total Size = ". ByteSize($totalSize);

}
