<?php
http_response_code(200);
echo json_encode(["error" => 0]);

return;
require "/var/www/html/public/index.php";

function ol1($str)
{
    file_put_contents("/var/glx/weblog/ncbd_editor_call_back_docx.log", date("Y-m-d H:i:s") . "#" . $str . "\n", FILE_APPEND);
}

function olSaveDoc($str){
    file_put_contents("/var/glx/weblog/ncbd_error_save_file_doc.log", date("Y-m-d H:i:s") . "#" . $str . "\n", FILE_APPEND);

}

ol1("-------------- CAll cb " . $_SERVER['REQUEST_URI'] . " \n * IP = " . $_SERVER['REMOTE_ADDR'] . " \n REAUEST: " . json_encode($_REQUEST));
ol1(" INPUT: " . json_encode(file_get_contents("php://input")));

try {
    $json = file_get_contents("php://input");
    $std = json_decode($json);

    if ($std->status == 2) {
        try {

            $key = $std->key ?? '';
            if(!str_contains($key, '-')){
                loi("Key not valid $key");
            }
            list($uid, $siteId) = @explode("-", $key);
            $file = DEF_FILE_PATH_IMPORT_EXCEL . ".$siteId.$uid.xlsx";

            $url = $std->url;
            if (!$cont = @file_get_contents($url))
                loi("Can not get content url '$url'");
            ol1(" ---- Save New Content \n -- $file \n -- $url");

            file_put_contents($file, $cont);
        }
        catch (Exception $e) {
            ol1(" *** Error: " . $e->getMessage());
            olSaveDoc(" *** Error: " . $e->getMessage());
            http_response_code(200);
            echo json_encode(["error" => 1]);
            exit;
        }

        ol1(" ---- Done Save Content '");
    }

    ol1(" ---- CallBack Done ?");

    http_response_code(200);
    echo json_encode(["error" => 0]);
    exit;
} catch (Exception $e) {
    ol1(" *** Error: " . $e->getMessage());
    http_response_code(200);
    echo json_encode(["error" => 1]);
    exit;
}
