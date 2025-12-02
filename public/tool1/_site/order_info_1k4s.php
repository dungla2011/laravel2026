<a href="/"> RETURNx </a>

<?php

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = '1k.4share.vn';

require_once "/var/www/html/public/index.php";



$link = \LadLib\Common\UrlHelper1::getUriWithoutParam();

echo "<br/>\n";



if(request('get_all') && isAdminLrv_()){
    $mm = \App\Models\OrderInfo::where('created_at' ,'>' ,'2024-04-20 00:00:00')->latest('id')->get();
}
else{
    //Tìm tất cả các user_id distingui trong DownloadLog model
    $distinctUserIds = \App\Models\DownloadLog::query()
        ->where("user_id_file", 3)
        ->where('count_dl', '>', 0)
        ->distinct()
        ->pluck('user_id');
    $mUidDownloadFile3 = $distinctUserIds->toArray();
    $mm = \App\Models\OrderInfo::where('created_at' ,'>' ,'2024-04-20 00:00:00')->whereIn('user_id', $mUidDownloadFile3)->latest('id')->get();
}

$cc = 0;
$tt = count($mm);

$ttM = 0;

$mmMoney = [];
$lastRow = '';
foreach ($mm AS $obj){
    $std = new stdClass();
    if($obj->money >= 200000){
        $obj->money = 50000;
    }

    $std->money = $obj->money;
    $std->date = substr($obj->created_at, 0, 10);
    $mmMoney[] = $std;
    $cc++;
    $ttM+=$obj->money;
    $user = \App\Models\User::find($obj->user_id);

    if($cc < 100)
        $lastRow .= "\n$cc/$tt. $obj->money | $obj->created_at <br/>";
}

$statistics = [];

foreach ($mmMoney as $obj) {
    $date = $obj->date;
    $money = $obj->money;

    if (!isset($statistics[$date])) {
        $statistics[$date] = ['count' => 1, 'revenue' => $money, 'details' => [$money]];
    } else {
        $statistics[$date]['count']++;
        $statistics[$date]['revenue'] += $money;
        $statistics[$date]['details'][] = $money;
    }
}

echo "<table border='1'>";
echo "<tr><th>Date</th><th>Count</th><th>Revenue</th><th>Formula</th></tr>";
$totalCount = 0;
$totalRevenue = 0;

foreach ($statistics as $date => $data) {
    $count = $data['count'];
    $revenue = $data['revenue'];
    $details = $data['details'];

    $totalCount += $count;
    $totalRevenue += $revenue;

    $formula = '';
    $detailCounts = array_count_values($details);
    foreach ($detailCounts as $value => $times) {
        $formula .= ($formula ? ' + ' : '') . "$times * $value";
    }

    echo "<tr><td>$date</td><td>$count</td><td>" . number_format($revenue) . "</td><td>$formula = " . number_format($revenue) . "</td></tr>";
}

// Add a row for the total count and revenue
echo "<tr><td>Total</td><td>$totalCount</td><td>" . number_format($totalRevenue) . "</td><td></td></tr>";

echo "</table>";


//echo "<hr> Last 100: ";
//echo "<br/>\n";
//echo "\n $lastRow";
