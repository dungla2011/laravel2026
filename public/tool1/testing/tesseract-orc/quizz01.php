<?php

/**
 * 05052023: test ok on linux
 *
 * composer require thiagoalessio/tesseract_ocr
 * apt install tesseract-ocr
 * apt install tesseract-ocr-vie
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'test2023.mytree.vn';

require_once '../../../index.php';

if (! isCli()) {
    exit('Not CLI!');
}

$mm = \App\Models\OcrImage::all();

foreach ($mm as $ocr) {
    echo "\n $ocr->name , $ocr->note";
    if ($ocr->note) {
//        $ocr->note : là số bài trong trang này, nhập tay?
        for ($i = 1; $i <= $ocr->note; $i++) {
            $m1 = $ocr->toArray();
            unset($m1['id']);
            unset($m1['created_at']);
            unset($m1['updated_at']);
            $m1['name'] .= "($i)";
            if (! \App\Models\QuizQuestion::where('name', $m1['name'])->first()) {
                echo "\n Insert ok";
                \App\Models\QuizQuestion::insert($m1);
            } else {
                echo "\n Not insert";
            }
        }
    }
}
