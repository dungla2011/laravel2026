<?php

$url = 'http://127.0.0.1:9081/api/member-file/upload';

$cFile = curl_file_create('c:\\Users\\pc1\\images\\a3.jpg');

//$res  = $this->post($url, ['file'=>$cFile, 'set_parent_id'=>0]);
//$res  = $this->post($url, ['set_parent_id'=>0]);

$cf = new \CURLFile('c:\\Users\\pc1\\images\\a3.jpg');

$post = ['set_parent_id' => 0, 'file_data' => $cf];
$ch = curl_init();

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    //'Content-Type: application/json',
    'Authorization: Bearer 123456',
]);
$result = curl_exec($ch);

echo '<pre> >>> '.__FILE__.'('.__LINE__.')<br/>';
print_r($result);
echo '</pre>';

$error_msg = curl_error($ch);
