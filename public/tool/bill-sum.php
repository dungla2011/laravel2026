
<meta name="viewport" content="width=device-width" />

<?php
use App\Models\FileUpload;
use App\Models\OrderItem;

use \Carbon\Carbon;

error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "/var/www/html/public/index.php";

if(!isSupperAdmin_()){
    die("Not valid info");
}

echo "<a href='/admin'> HOMEGLX</a>: ". nowyh();
echo "\n<hr/>\n";

$orders = OrderItem::where('created_at', '>', now()->subMonths(6))
    ->orderBy('created_at', 'desc')
    ->get()
    ->groupBy(function($date) {
        return \Carbon\Carbon::parse($date->created_at)->format('Y-m');
    });

foreach ($orders as $month => $orderGroup) {
    $totalPrice = $orderGroup->sum('price') / 1000;
    echo "\n{$month}: <b> {$totalPrice} </b> | ";
}

$orders = \App\Models\OrderInfo::where('created_at', '>', now()->subDays(10))
    ->orderBy('created_at', 'desc')
    ->get()
    ->groupBy(function($date) {
        return \Carbon\Carbon::parse($date->created_at)->format('Y-m-d');
    });

echo "<hr/>\n";
foreach ($orders as $date => $orderGroup) {
    $totalPrice = $orderGroup->sum('money') / 1000;
    $orderCount = $orderGroup->count();
    $baokimTotal = $orderGroup->where('vendor_pay', 'baokim')->sum('money') / 1000;
    $momoTotal = $orderGroup->where('vendor_pay', 'momo')->sum('money') / 1000;
    echo "\n {$date} : <b>{$totalPrice}</b> ({$orderCount})={$baokimTotal}+{$momoTotal}mm | ";
}


$mm = \App\Models\OrderItem::where("created_at", '>', nowyh(time() - 10*_NSECOND_DAY))->orderBy('created_at', 'desc')->get();
echo "<hr/>\n";
$cc = 0;
foreach ($mm AS $order){
    if(!$order->price)
        continue;
    $cc++;
    echo "\n$cc. $order->user_id , $order->price, $order->created_at";

    //Tim file user tai:
    \App\Models\TmpDownloadSession::where("user_id", $order->user_id)
        ->where("created_at", '>', $order->created_at)
        ->orderBy('created_at', 'asc')
        ->limit(3)
        ->get()
        ->each(function($item){
            static $count = 0; // Biến đếm tĩnh
            $count++;

            $file = FileUpload::find($item->fid);
            if(!$file)
                return;
            $em = \App\Models\User::find($file->user_id)?->email;

            $byteSize = ByteSize($file->file_size);
            $pad = '';
            if($count == 1)
                $pad = " ; color: red;";
            echo "<br> <span style='font-size: small; color: gray'> + DLID.$item->id, $item->fid, {$item->created_at}, <span style='$pad'>$em</span> - $file->user_id, $byteSize, (FTIME : $file->created_at) $file->name </span>";
        });

    echo "<br/>\n";

}
