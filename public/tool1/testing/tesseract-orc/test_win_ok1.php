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

$file = '/share/2/3.png';

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
