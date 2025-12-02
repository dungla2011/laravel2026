<?php

require_once __DIR__.'/../index.php';
//echo "\n abc";

if (! headers_sent()) {
    header_remove();
}
header('Content-Type:image/jpeg');

$fid = request('fid');

$ofile = \App\Models\FileUpload::find($fid);
$fileCloud = \App\Models\FileCloud::find($ofile->cloud_id);
if (file_exists($fileCloud->file_path)) {
    $filepath = $fileCloud->file_path;
} else {
    exit('File not found!');
}
readfile($filepath);
