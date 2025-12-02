<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
$GLOBALS['DISABLE_DEBUG_BAR'] = 0;
error_reporting(E_ALL);
ini_set('display_errors', 1);


require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

die("Not use this");

if(!isAdminACP_()){
    die("Not admin ?");
}

$evId = request('_event_id_');
//$evId = 126;

if(!$evId){
    die("Not event id?");
}

$meta = \App\Models\EventUserInfo::getMetaObj();
$mField = $meta->getShowGetOneAllowFieldList(1);

// Check if the value was found before unsetting
//if ($key !== false) {
//    unset($colors[$key]);
//}

//giữ lại các key này thôi id,title,first_name,last_name,designation,organization,email,phone,address,id_number,tax_number,bank_acc_number,bank_name_text
$mField = array_intersect($mField, [
    'id',
    'title',
    'first_name',
    'last_name',
    'designation',
    'organization',
    'email',
    'phone',
    'address',
    'id_number',
    'tax_number',
    'bank_acc_number',
    'bank_name_text'
]);


$mm = \App\Models\EventAndUser::where("event_id", $evId)->pluck('user_event_id')->toArray();
$mm1 = \App\Models\EventUserInfo::select($mField)->whereIn("id", $mm)->get()->toArray();


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

array_to_csv_download($mm1, // this array is going to be the second row
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
