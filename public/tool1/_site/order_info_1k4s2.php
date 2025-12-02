<a href="/"> RETURNx </a>

<?php

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = '1k.4share.vn';

require_once "/var/www/html/public/index.php";

$link = \LadLib\Common\UrlHelper1::getUriWithoutParam();
if(!isSupperAdmin_()){
    die("Not admin!");
}

echo "<br/>\n";

//Tìm tất cả các user_id distingui trong DownloadLog model
$distinctUserIdDownloadFiles = \App\Models\DownloadLog::query()
    ->where('count_dl', '>', 0)
    ->distinct()
    ->pluck('user_id');
$mUidDownloadFile = $distinctUserIdDownloadFiles->toArray();

$userDownloadCounts = \App\Models\DownloadLog::query()
    ->where('count_dl', '>', 0)
    ->pluck('user_id_file')
    ->countBy()->toArray();

arsort($userDownloadCounts);
$totalDownloads = array_sum($userDownloadCounts);

echo "<table border='1'>";
echo "<tr><th>User ID</th><th>Count</th><th>Email</th><th>Percentage of Total</th></tr>";

$cc = 0;
foreach ($userDownloadCounts AS $uid => $count){
    $cc++;
    if(!$uid || $count < 3 || $cc > 10)
        continue;

    $user = \App\Models\User::find($uid);
    $percentage = ($count / $totalDownloads) * 100;

    echo "<tr><td>$uid</td><td>$count</td><td>$user->email</td><td>" . number_format($percentage, 2) . "%</td></tr>";
}

echo "</table>";

echo "<br/>\n";

$mmBuy = \App\Models\OrderInfo::where('created_at' ,'>' ,'2024-04-20 00:00:00')->latest('id')->get();

$cc = 0;
$tt = count($mmBuy);

$ttM = 0;

$mmMoneyAndInfo = [];
$lastRow = '';
foreach ($mmBuy AS $objBuy){
    $std = new stdClass();
    $std->money = $objBuy->money;
    $std->date = substr($objBuy->created_at, 0, 10);
    $std->have_download = 0;
    if(in_array($objBuy->user_id, $mUidDownloadFile)){
        $std->have_download = 1;
    }

    $mmMoneyAndInfo[] = $std;
    $cc++;
    $ttM+=$objBuy->money;
    $user = \App\Models\User::find($objBuy->user_id);
    if($cc < 100)
        $lastRow .= "\n$cc/$tt. $objBuy->money | $objBuy->created_at | $user->email <br/>";
}


$statistics = [];
$statisticsM = [];
foreach ($mmMoneyAndInfo as $obj) {
    $date = $obj->date;
    $month = date('Y-m', strtotime($obj->date));

    $money = $obj->money;
    $haveDl = $obj->have_download;

    if (!isset($statistics[$date])) {
        $statistics[$date] = ['count' => 1, 'revenue' => $money, 'details' => [$money], 'haveDownload' => $haveDl];
    } else {
        $statistics[$date]['count']++;
        $statistics[$date]['revenue'] += $money;
        $statistics[$date]['haveDownload'] += $haveDl;

        $statistics[$date]['details'][] = $money;
    }

    if (!isset($statisticsM[$month])) {
        $statisticsM[$month] = $money;
    } else {
        $statisticsM[$month] += $money;
    }
}
foreach ($statisticsM AS $month => $money){
    if($month < date("Y-m", time() - _NSECOND_DAY * 90))
        continue;
    $money = number_format($money);
    echo " $month: <b> $money </b> | ";
}
echo "<br/>\n";

echo "<table border='1'>";
echo "<tr><th>Date</th><th>Count</th><th>Revenue</th><th>Formula</th></tr>";
$totalCount = 0;
$totalRevenue = 0;

foreach ($statistics as $date => $data) {
    $count = $data['count'];
    $revenue = $data['revenue'];
    $details = $data['details'];
    $haveDownload = $data['haveDownload'];

    $totalCount += $count;
    $totalRevenue += $revenue;

    $formula = '';
    $detailCounts = @array_count_values($details);
    foreach ($detailCounts as $value => $times) {
        $formula .= ($formula ? ' + ' : '') . "$times * $value";
    }

    echo "<tr><td>$date</td><td>$count (haveDl = $haveDownload) </td><td>" . number_format($revenue) . "</td><td>$formula = " . number_format($revenue) . "</td></tr>";
}

// Add a row for the total count and revenue
echo "<tr><td>Total</td><td>$totalCount</td><td>" . number_format($totalRevenue) . "</td><td></td></tr>";

echo "</table>";


echo "<hr> Last 100: ";
echo "<br/>\n";
echo "\n $lastRow";
