<?php

function output($filename, $string, $createFolder = 0)
{
    if ($createFolder && ! file_exists(dirname($filename))) {
        mkdir(dirname($filename));
    }

    $file = @fopen($filename, 'a');
    if (! $file) {
        return;
    }
    @fwrite($file, $string."\r\n");
    @fclose($file);
}

$filename = 'e:/1/file100_MB';
unlink($filename);
$all = '';

for ($i = 0; $i < 10000000; $i++) {
    $str = sprintf('%09d', $i);
    echo "\n $str ";
    $all .= $str."\n";
    if ($i % 10000 == 0) {
        output($filename, $all);
        $all = '';
    }
}

//file_put_contents($filename,$all."\n",FILE_APPEND);

//
//$all = "";
//for($i = 0; $i < 10000000; $i++){
//
//    $str = sprintf("%09d", $i);
//    echo "\n $str ";
//    $all .= $str . "\n";
//
//}
//file_put_contents($filename,$all."\n",FILE_APPEND);
