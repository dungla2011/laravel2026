<?php

/**
 * Code này có cả php để gen token,
 * Tại sao cần sinh token mỗi lần refresh? vì nếu ko token sẽ hết hạn báo lỗi
 * - Gốc từ : viewsource của example chạy được của onlyoffice, https://doc1.pm33.net/example/
 * (Cài đặt single click https://helpcenter.onlyoffice.com/installation/docs-community-install-script.aspx)
 *
 * Sau đó copy source sang đây, rồi copy CODE php vào
 * Lấy token từ "vi /etc/onlyoffice/documentserver/local.json"  --- "secret": { "inbox": { "string": "FSeGbZpN5zKof21FjKdbywrzyhJXouUp"
 * Xem file này cách sinh ra config+ token từ php: public/train/OnlyOffice/php-example1/src/views/DocEditorView.php
 * Lấy mã config từ mã viewsource của example, chuyển sang dạng PHP rồi Encode với JWT, thêm token vào config
 *
 */

namespace Example;

//require_once __DIR__ . '/vendor/autoload.php';
require_once "/var/www/html/public/train/OnlyOffice/php-example1/vendor/autoload.php";
require_once '/var/www/html/public/train/OnlyOffice/php-example1/src/ajax.php';
require_once '/var/www/html/public/train/OnlyOffice/php-example1/src/functions.php';
require_once '/var/www/html/public/train/OnlyOffice/php-example1/src/trackmanager.php';


use Example\Common\HTTPStatus;
use Example\Common\URL;
use Example\Configuration\ConfigurationManager;
use Example\Helpers\JwtManager;
use Example\Views\ForgottenFilesView;
use Example\Views\DocEditorView;
use Example\Views\IndexView;

$jwtManager = new JwtManager();

$config = [
    "document" => [
        "directUrl" => "",
        "fileType" => "docx",
        "info" => [
            "owner" => "Me",
            "uploaded" => "Sun Oct 27 2024",
            "favorite" => null
        ],
        "key" => "118.71.162.143__127.0.0.1new.docx31730036481292",
        "permissions" => [
            "chat" => true,
            "comment" => true,
            "copy" => true,
            "download" => true,
            "edit" => true,
            "fillForms" => true,
            "modifyContentControl" => true,
            "modifyFilter" => true,
            "print" => true,
            "review" => true,
            "reviewGroups" => null,
            "commentGroups" => [],
            "userInfoGroups" => null,
            "protect" => true
        ],
        "referenceData" => [
            "fileKey" => "{\"fileName\":\"new.docx\",\"userAddress\":\"118.71.162.143__127.0.0.1\"}",
            "instanceId" => "https://doc1.pm33.net/example"
        ],
        "title" => "new.docx",
        "url" => "https://doc1.pm33.net/example/download?fileName=new.docx&useraddress=118.71.162.143__127.0.0.1"
    ],
    "documentType" => "word",
    "editorConfig" => [
        "actionLink" => null,
        //"callbackUrl" => "https://doc1.pm33.net/example/track?filename=new.docx&useraddress=118.71.162.143__127.0.0.1",
        "callbackUrl" => "https://doc2.mytree.vn/train/OnlyOffice/lad002-001-php-gen-token-call-back.php",
        "coEditing" => null,
        "createUrl" => "https://doc1.pm33.net/example/editor?fileExt=docx&userid=uid-1&type=desktop&lang=en",
        "customization" => [
            "about" => true,
            "comments" => true,
            "close" => ["visible" => false],
            "feedback" => true,
            "forcesave" => false,
            "goback" => ["blank" => false, "url" => "https://doc1.pm33.net/example"],
            "submitForm" => true
        ],
        "embedded" => [
            "embedUrl" => "https://doc1.pm33.net/example/download?fileName=new.docx",
            "saveUrl" => "https://doc1.pm33.net/example/download?fileName=new.docx",
            "shareUrl" => "https://doc1.pm33.net/example/download?fileName=new.docx",
            "toolbarDocked" => "top"
        ],
        "fileChoiceUrl" => "",
        "lang" => "en",
        "mode" => "edit",
        "plugins" => ["pluginsData" => []],
        "templates" => [
            [
                "image" => "",
                "title" => "Blank",
                "url" => "https://doc1.pm33.net/example/editor?fileExt=docx&userid=uid-1&type=desktop&lang=en"
            ],
            [
                "image" => "https://doc1.pm33.net/example/images/file_docx.svg",
                "title" => "With sample content",
                "url" => "https://doc1.pm33.net/example/editor?fileExt=docx&userid=uid-1&type=desktop&lang=en&sample=true"
            ]
        ],
        "user" => [
            "group" => "",
            "id" => "uid-3",
            "image" => "https://doc1.pm33.net/example/images/uid-1.png",
            "name" => "LAD 1"
        ]
    ]
];

// jwtSecret get from vi /etc/onlyoffice/documentserver/local.json"  --- "secret": { "inbox": { "string": "FSeGbZpN5zKof21FjKdbywrzyhJXouUp"
$tk = $jwtManager->jwtEncode($config, "VBqpuj5FjAXwTpaUnYD5lcSIRqlsEkoF");

$config['token'] = $tk;
$js = json_encode($config, JSON_PRETTY_PRINT );
?>

<!DOCTYPE html>
<html style="height: 100%;">
<head>    <title>Lad OnlyOf 02 OK</title></head>
<body style="height: 100%; margin: 0;">
<div id="placeholder" style="height: 100%"></div>
<script type="text/javascript" src="http://103.163.217.6:8080/web-apps/apps/api/documents/api.js"></script>
<script type="text/javascript">

    var config = <?php echo $js; ?>;

    window.docEditor = new DocsAPI.DocEditor("placeholder", config);

</script>
</body>
</html>
