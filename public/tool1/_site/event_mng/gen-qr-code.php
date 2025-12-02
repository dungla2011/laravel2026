<style>
    * {
        background-color: blue;
    }
</style>
<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '/var/www/html/public/index.php';

$str = $_GET['str'] ?? 'need_str';

$time = time();
//for($i = 0; $i<200; $i++)

$png = SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->margin(1)->format('png')->generate($str);

//echo "\n dTIME = ". (time() - $time);
//return;

$pngS = base64_encode($png);
//echo $png;
//
//return;
$img = 'data:image/png;base64,'.$pngS;

echo "\n <img src='$img'>";
