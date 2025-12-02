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
    "type" => "desktop",
    "documentType" => "word",
    "document" => [
        "title" => "new.docx",
        "url" => "http://103.163.217.4/download?fileName=new.docx&userAddress=118.71.162.143",
        "directUrl" => "",
        "fileType" => "docx",
        "key" => "1141664094",
        "info" => [
            "owner" => "Me",
            "uploaded" => "01.11.24",
            "favorite" => null
        ],
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
        "referenceData" => [
            "fileKey" => "{\"fileName\":\"new.docx\"}",
            "instanceId" => "http://103.163.217.4"
        ]
    ],
    "editorConfig" => [
        "actionLink" => null,
        "mode" => "edit",
        "lang" => "en",
        "callbackUrl" => "http://103.163.217.4/track?fileName=new.docx&userAddress=118.71.162.143",
        "coEditing" => null,
        "createUrl" => "http://103.163.217.4/editor?fileExt=docx&user=uid-1",
        "templates" => [
            [
                "image" => "",
                "title" => "Blank",
                "url" => "http://103.163.217.4/editor?fileExt=docx&user=uid-1"
            ],
            [
                "image" => "http://103.163.217.4/assets/images/file_docx.svg",
                "title" => "With sample content",
                "url" => "http://103.163.217.4/editor?fileExt=docx&user=uid-1&sample=true"
            ]
        ],
        "user" => [
            "id" => "uid-1",
            "name" => "John Smith",
            "group" => "",
            "image" => "http://103.163.217.4/assets/images/uid-1.png"
        ],
        "embedded" => [
            "saveUrl" => "http://103.163.217.4/download?fileName=new.docx",
            "embedUrl" => "http://103.163.217.4/download?fileName=new.docx",
            "shareUrl" => "http://103.163.217.4/download?fileName=new.docx",
            "toolbarDocked" => "top"
        ],
        "customization" => [
            "about" => true,
            "comments" => true,
            "feedback" => true,
            "forcesave" => false,
            "submitForm" => false,
            "goback" => [
                "blank" => false,
                "url" => "http://103.163.217.4"
            ]
        ]
    ],
    // "token" => "eyJ0eXAiOiJKV1QiLCJhbGc"
];

$jwtManager = new JwtManager();
$tk = $jwtManager->jwtEncode($config);

$config['token'] = $tk;

$js_config = json_encode($config, JSON_PRETTY_PRINT );


?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1,
            maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="mobile-web-app-capable" content="yes" />
    <link rel="icon" href="assets/images/word.ico" type="image/x-icon" />
    <title>ONLYOFFICE</title>

    <style>
        html {
            height: 100%;
            width: 100%;
        }

        body {
            background: #fff;
            color: #333;
            font-family: Arial, Tahoma,sans-serif;
            font-size: 12px;
            font-weight: normal;
            height: 100%;
            margin: 0;
            overflow-y: hidden;
            padding: 0;
            text-decoration: none;
        }

        form {
            height: 100%;
        }

        div {
            margin: 0;
            padding: 0;
        }
    </style>

    <script type="text/javascript" src="http://103.163.217.6:8080/web-apps/apps/api/documents/api.js">
    </script>

    <script type="text/javascript">

        var docEditor;
        var config;
        let history;


        var сonnectEditor = function () {



            config = <?php echo $js_config; ?>;

            config.width = "100%";
            config.height = "100%";

            docEditor = new DocsAPI.DocEditor("iframeEditor", config);
        };

        if (window.addEventListener) {
            window.addEventListener("load", сonnectEditor);
        } else if (window.attachEvent) {
            window.attachEvent("load", сonnectEditor);
        }

    </script>
</head>
<body>
<form id="form1">
    <div id="iframeEditor">
    </div>
</form>
</body>
</html>
