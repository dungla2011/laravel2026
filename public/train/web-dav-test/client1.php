<?php

/**
 * LAD 17.10.24
 * window map network driver
 * Đơn giản thế này có thể tạo được folder, upload được file OK!
 * Chú ý folder  cần có quyền Write
 *
 */

function ol3($str)
{
    $bname = basename(__FILE__);
    file_put_contents("/var/glx/weblog/$bname.log", $str . "\n", FILE_APPEND);
}
function isFileLocked($filePath)
{
    $file = fopen($filePath, 'r');
    if (!$file) {
        return false;
    }

    $locked = !flock($file, LOCK_EX | LOCK_NB);
    fclose($file);

    return $locked;
}

$filePath = "/share/dav/siteid_41/1/Zscaler.zip";
echo filesize("/share/dav/siteid_41/1/Zscaler.zip");

echo "<br/>\n LOCK = " . isFileLocked($filePath);

return;
require "../../index.php";

$url = 'https://mytree.vn/train/web-dav-test/webdav0.php/a3.jpg';
$url = 'https://mytree.vn/train/web-dav-test/webdav21.php/a3.jpg';
//$url = 'https://mytree.vn/train/web-dav-test/webdav21.php/New folder (3)';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
print_r($response);
echo "</pre>";

if ($httpCode == 204) {
    echo "File deleted successfully.";
} else {
    echo "Failed to delete file. HTTP status code: " . $httpCode;
}
