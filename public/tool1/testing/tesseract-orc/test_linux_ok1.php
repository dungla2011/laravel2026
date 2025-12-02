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

require_once '../../../index.php';

echo "<br/>\n ".__LINE__.' -- '.time();

echo "<br/>\n ".__LINE__.' -- '.time();

use thiagoalessio\TesseractOCR\TesseractOCR;

$file = __DIR__.'/2.jpg';

//$file = "c:\\Users\\lad\\Desktop\\1.png";
//$file = "c:\\1\\1.jpg";

echo "<br/>\n RESULT OCR: $file ";

echo '<hr>';

$time1 = microtime(1);
echo "<br/>\n Start1: ".time();

if (isWindow1()) {
    $ret = (new TesseractOCR($file))->lang('vie')->hocr()->executable('c:\\Program Files (x86)\\Tesseract-OCR\\tesseract.exe')->run();
} else {
    $ret = (new TesseractOCR($file))->lang('vie')->run();
}

echo "<br/>\nRET = $ret";

file_put_contents('/share/'.time().'.html', $ret);

for ($i = 0; $i < 100; $i++) {
    $ret = str_replace("\n ", "\n", $ret);
    $ret = str_replace("\n\n\n\n", "\n\n\n", $ret);
}

echo "\n----\n";
print_r($ret);
