<?php

function ol1($str)
{
    file_put_contents("/var/glx/weblog/" . basename(__FILE__).'.log',  date("Y-m-d H:i:s") ."#". $str . "\n", FILE_APPEND);
}

ol1("-------------- CAll cb " . $_SERVER['REQUEST_URI'] . " \n * IP = " . $_SERVER['REMOTE_ADDR'] . " \n REAUEST: " . json_encode($_REQUEST));
ol1(" INPUT: " . json_encode(file_get_contents("php://input")));
http_response_code(200);
echo json_encode(["error" => 0]);
