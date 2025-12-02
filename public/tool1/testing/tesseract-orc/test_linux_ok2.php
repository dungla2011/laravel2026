<?php

/**
 * 05052023: Nhận dạng ảnh của Đánh thức tài năng toán học
 * danh thuc tai nang toan hoc
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'test2023.mytree.vn';

require_once '../../../index.php';

$dir = '/share/DanhThucTaiNangToan7-8Tuoi';
$dir = '/share/DanhThucTaiNangToan8-9Tuoi';
$dir = '/share/DanhThucTaiNangToan9-10Tuoi';

ListDirFullToArray($dir, $m1);

if (! isCli()) {
    exit('Not CLI!');
}

echo "<br/>\n ".__LINE__.' -- '.time();

echo "<br/>\n ".__LINE__.' -- '.time();

use thiagoalessio\TesseractOCR\TesseractOCR;

$us = \App\Models\User::where('email', 'dungla2011@gmail.com')->first();

if (! $us) {
    exit('Not us!');
}
$userid = $us->id;

foreach ($m1 as $file) {

    //$file = __DIR__.'/2.jpg';

    //$file = "c:\\Users\\lad\\Desktop\\1.png";
    //$file = "c:\\1\\1.jpg";
    $bname = basename($file);

    echo "\n Bname = $bname";
    $fidUp = 0;
    if (! $fileO = \App\Models\FileUpload::where('name', $bname)->first()) {
        $fidUp = \App\Models\FileUpload::uploadFileContentByApi0('https://test2023.mytree.vn/api/member-file/upload', $us->getUserToken(), $file);
    } else {
        echo "\n Đã upload từ trước";
        $fidUp = $fileO->id;
    }

    echo "\n";
    //    getch("... ID = $fidUp");

    if (! $quzz = \App\Models\OcrImage::where('name', $bname)->first()) {
        \App\Models\OcrImage::insert(['name' => $bname, 'user_id' => $userid, 'image_list' => $fidUp]);
    }

    if (! $quzz = \App\Models\OcrImage::where('name', $bname)->first()) {
        exit("\n error insert?");
    }

    if (! $quzz->image_list) {
        $quzz->image_list = $fidUp;
        $quzz->update();
    }

    echo "<br/>\n RESULT OCR: $file ";

    echo '<hr>';

    $time1 = microtime(1);
    echo "<br/>\n Start1: ".time();

    if (isWindow1()) {
        $ret = (new TesseractOCR($file))->lang('vie')->hocr()->executable('c:\\Program Files (x86)\\Tesseract-OCR\\tesseract.exe')->run();
    } else {
        $ret = (new TesseractOCR($file))->lang('vie')->run();
    }

    //file_put_contents("/share/".time().".html", $ret);

    for ($i = 0; $i < 100; $i++) {
        $ret = str_replace("\n ", "\n", $ret);
        $ret = str_replace("\n\n\n\n", "\n\n\n", $ret);
    }

    echo "\n----\n";
    print_r($ret);

    if ($ret) {
        $quzz->draft = $ret;
        $quzz->update();
    }

}
