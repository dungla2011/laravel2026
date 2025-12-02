<?php
namespace Example;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// require_once "/var/www/html/public/train/OnlyOffice/php-example1/vendor/autoload.php";
// require_once '/var/www/html/public/train/OnlyOffice/php-example1/src/ajax.php';
// require_once '/var/www/html/public/train/OnlyOffice/php-example1/src/functions.php';
// require_once '/var/www/html/public/train/OnlyOffice/php-example1/src/trackmanager.php';


use Example\Common\HTTPStatus;
use Example\Common\URL;
use Example\Configuration\ConfigurationManager;
use Example\Helpers\JwtManager;
use Example\Views\ForgottenFilesView;
use Example\Views\DocEditorView;


require_once __DIR__ . '/php-example1/vendor/autoload.php';

putenv('STORAGE_PATH=/var/glx/docs');
putenv('DOCUMENT_SERVER_PUBLIC_URL=http://103.163.217.6:8080');
#ADDRESS
#PORT
#DOCUMENT_SERVER_PRIVATE_URL
#DOCUMENT_SERVER_PUBLIC_URL
#EXAMPLE_URL

putenv('JWT_SECRET=VBqpuj5FjAXwTpaUnYD5lcSIRqlsEkoF');


$config = [
    'document' => [
        'fileType' => 'docx',
        'key' => '12366612',
        'title' => 'Doc2222.docx',
        'url' => 'http://103.163.216.21/public/myDoc2.docx',
        "permissions" => [
            "comment" => true,
            "copy" => true,
            "download" => true,
            "edit" => true,
            "print" => true,
            "fillForms" => true,
            "modifyFilter" => true,
            "modifyContentControl" => true,
            "review" => true,
            "chat" => true,
            "reviewGroups" => null,
            "commentGroups" => [],
            "userInfoGroups" => null,
            "protect" => true
        ],
    ],
    "editorConfig" => [
        //Chú ý call back phải trả lại http_response_code(200); echo json_encode(["error" => 0]);
        //Để không báo lỗi
        "callbackUrl" => "http://103.163.216.21/train/OnlyOffice/lad0051-callback.php",
        "user" => [
            "id" => "uid-1",
            "name" => "Lad1",
            "group" => "",
//            "image" => "http://103.163.217.4/assets/images/uid-1.png"
        ],
    ],
    'documentType' => 'word',
];

$jwtManager = new JwtManager();
$tk = $jwtManager->jwtEncode($config);
$config['token'] = $tk;

$js_config = json_encode($config, JSON_PRETTY_PRINT );
?>
<div id="placeholder"></div>

<script type="text/javascript" src="http://103.163.217.6:8080/web-apps/apps/api/documents/api.js"></script>

<script>
    var onRequestRename = function(event) { //  the user is trying to rename file by clicking Rename... button
        innerAlert("onRequestRename: " + JSON.stringify(event.data));

        var newfilename = event.data;
        var data = {
            newfilename: newfilename,
            dockey: config.document.key,
            ext: config.document.fileType
        };

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "rename");
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.send(JSON.stringify(data));
        xhr.onload = function () {
            innerAlert(xhr.responseText);
        }
    };


    var config = <?php echo $js_config; ?>;

    config.events = {

    };

    config.events['onRequestRename'] = onRequestRename;


    const docEditor = new DocsAPI.DocEditor("placeholder", config);
</script>
