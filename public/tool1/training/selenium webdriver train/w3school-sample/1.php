<?php

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'misu.mytree.vn';

require_once '/var/www/html/public/index.php';
//require_once 'qA_W3School.php';
require_once '/var/www/html/app/Components/qA_W3School.php';
require_once '/var/www/html/vendor/_ex/simple_html_dom.php';

$url = 'https://www.w3schools.com/css/exercise.asp';
$url = 'https://www.w3schools.com/html/exercise.asp?filename=exercise_html_classes1';
$html = file_get_contents($url);

$obj = new qA_W3School($html);

echo '<pre> >>> '.__FILE__.'('.__LINE__.')<br/>';
print_r($obj);
echo '</pre>';
