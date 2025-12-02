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
        'key' => '111122223333',
        'title' => 'Doc2222.docx',
        'url' => 'http://103.163.216.21/public/myDoc2.docx',
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
    const docEditor = new DocsAPI.DocEditor("placeholder", <?php echo $js_config; ?>);
</script>
