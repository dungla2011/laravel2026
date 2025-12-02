<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
define('DEF_TOOL_CMS', 1);

if (! $_COOKIE['_tglx863516839']) {
    exit(' Need login!');
}

$params = $_REQUEST;

if (! isset($params['data'])) {
    exit('Not data to export?');

    return;
}

function getDomainHostName()
{
    if (isset($_SERVER['HTTP_HOST'])) {
        return explode(':', $_SERVER['HTTP_HOST'])[0];
    }
    if (isset($_SERVER['SERVER_NAME'])) {
        return $_SERVER['SERVER_NAME'];
    }

    return null;
}

$domain = getDomainHostName();

$data = $params['data'];
//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($data);
//echo "</pre>";
//return;
$mline = explode("\n", $data);
if (! $mline) {
    return;
}

$data1 = [];
foreach ($mline as $line) {
    $l1 = explode("\t", $line);
    if ($l1) {
        $data1[] = $l1;
    }
}

$ref = '';
$domain = getDomainHostName();
ob_clean();
//header("Content-Type: application/vnd.ms-excel");
//header('Content-Disposition: attachment; filename="'.$domain.'.data.xls"');

function array_to_csv_download($array, $filename = 'export.csv', $delimiter = ';')
{
    global $domain;
    header('Content-Encoding: UTF-8');
    header('Content-type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename='.$filename);
    echo "\xEF\xBB\xBF"; // UTF-8 BOM

    //    header('Content-Type: application/csv');
    //    header('Content-Disposition: attachment; filename="'.$filename.'";');

    // open the "output" stream
    // see http://www.php.net/manual/en/wrappers.php.php#refsect2-wrappers.php-unknown-unknown-unknown-descriptioq
    $f = fopen('php://output', 'w');

    @fputcsv($f, ['Export by: '.$domain], $delimiter);
    foreach ($array as $line) {
        @fputcsv($f, $line, $delimiter);
        //        echo "<br/>\n $line";
    }
}

array_to_csv_download($data1, // this array is going to be the second row
    "$domain.csv", ','
);

return;
?>
<html>
<head>
    <meta http-equiv="Content-type" content="text/html;charset=utf-8"/>
</head>

<style>
    body{
        font-size: 12px;
    }
</style>

<body>

<br>
<div style=''><?php //echo $mline[0]?> </div>
<table border="1">
    <?php
    $cc = 0;
foreach ($mline as $line) {
    //$line = trim($line);
    if (! $line) {
        continue;
    }
    $cc++;
    //Bỏ qua hàng đầu là thông tin phân trang
    //        if($cc == 1)
    //            continue;
    echo "\n<tr>";
    $mcell = explode("\t", $line);
    foreach ($mcell as $item) {
        if ($cc == 1) {
            echo "<th style='color: darkgreen'>$item</th>";
        } else {
            echo "<td>$item</td>";
        }
    }
    echo "\n</tr>";
}
?>

</table>


</body>
</html>
