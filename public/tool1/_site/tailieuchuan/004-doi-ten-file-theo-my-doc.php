<?php

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'tailieuchuan.net';

require "/var/www/html/public/index.php";

$mm = \App\Models\MyDocument::all();


die("stop...");

foreach ($mm AS $obj) {
    $id = $obj->id;
    $name = $obj->name;

    $fileList = $obj->file_list;

    $fid = explode(',', $fileList)[0];

    if($file = \App\Models\FileUpload::find($fid))
    {
        echo "\n<br> $id. $name  | $file->name";
//        if($file->name != $name . '.pdf' && $file->name != $name)
        if(!str_contains($file->name, 'TaiLieuChuan.net'))
        {
            $tmp = $file->name;

            $file->name = "TaiLieuChuan.net - " . $file->name;
//            $file->addLog("Sửa tên file theo tên MyDocument: $tmp -> $file->name");
//            $file->save();
            echo " | sửa tên file --> $file->name";
        }
    }




}


