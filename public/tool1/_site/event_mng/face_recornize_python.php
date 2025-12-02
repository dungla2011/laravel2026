<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require "/var/www/html/public/index.php";
$domain = getDomainHostName();

$url = "http://$domain:50000/get_face_vector";

if($link_file = $_GET['link_file'] ?? '')
{
    $link_file = HTS($link_file);
}
else{
    die("Not link file?");
}

//die("Link file1: $link_file");

//Post CURL
$postData = [
    //'image_link' => 'https://events.dav.edu.vn/test_cloud_file?fid=4866',
    'image_link' => $link_file,
];
$ch = curl_init($link_file);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
if(!$response = curl_exec($ch)){
    die("Face server not work???");
}

curl_close($ch);
print_r($response);
