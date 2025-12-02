<?php

//define("DEF_IGNORE_TEMPLATE", 1);
//require '/var/www/glx/test/lib2/index.php';

$file_path = "/var/www/4ShareTool.zip";

//$fname = preg_replace(' +', '-', basename($file_path));

ob_clean();
header('Cache-control: private');
header('Content-Type: application/octet-stream');
header('Content-Length: ' . filesize($file_path));
header('Content-Disposition: attachment; filename=' . basename($file_path));
flush();
readfile($file_path);
$remote_ip = getenv('REMOTE_ADDR');

file_put_contents("/var/glx/weblog/download_4ShareTool.log", $remote_ip . date("Y-m-d H:i:s"). " : 2013vers ", FILE_APPEND);

?>
