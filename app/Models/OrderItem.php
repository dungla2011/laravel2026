<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Laravel\Database\TraitModelExtra;

class OrderItem extends ModelGlxBase
{
    use HasFactory, SoftDeletes, TraitModelExtra, SnowflakeId;

    protected $guarded = [];

    /*
     * Ví dụ khi bill chưa có param1, thì cập nhật từ product
     * hoặc cập nhật từ SKU của product
     */
    function updateParam1AndGetProduct()
    {
        //Tìm sku, tìm product id, để update param1
        //Param1 dùng để tin so luot tai file
        //Có thể được lưu trong bảng sku hoặc product (nếu product chỉ có 1 sku, thì lưu tại product)
        if($prod = Product::find($this->product_id)){
//            echo "\n $prod->name / $prod->param1 / $oneBill->param1 ";
            if($this->param1 !=  $prod->param1) {
                $this->param1 =  $prod->param1;
                $this->addLog("Set param 1: $prod->param1");
                $this->save();
            }

            return $prod;
        }
        return null;
    }

    static public function updateCountDownloadUsed($uid) {

        $countDL = DownloadLog::where("user_id", $uid )->where('count_dl', '>', 0 )->count();
        //->whereIn('product_id', [58,59])
        $mBill = OrderItem::where("user_id", $uid)->get();
        $mBill0 = $mBill->toArray();
        foreach ($mBill0 AS &$one){
            $one['allow'] = $one['param1'];
        }
        $m1 = updateArrayBillAndFillUsedNumber($countDL, $mBill0);
        foreach ($m1 AS $obj){
            foreach ($mBill as $bill) {
                if($obj['id'] == $bill->id){
//                    dump($bill);
//                    echo "<br/>\n Update $bill->id / $bill->used /" . $obj['used'];
                    if($bill->used != $obj['used']){
//                        echo "<br/>\n Update1 xxxx $bill->id ";
                        $bill->used = $obj['used'];
                        $bill->save();
                    }
                }
            }
        }
    }

    /**
     * @param $orders
     * @param $checkDate
     * @return int|mixed
     *
     *
     */


    /*


// Test cases
$orders = [
    [
        'date' => '2024-01-01 00:00:00',
        'nSeconds' => 3600 * 24 * 10
    ],
    [
        'date' => '2024-01-01 00:00:00',
        'nSeconds' => 3600 * 24 * 1
    ],
    [
        'date' => '2024-01-05 00:00:00',
        'nSeconds' => 3600* 24 * 1
    ],
    [
        'date' => '2024-01-08 00:00:00',
        'nSeconds' => 3600 * 24 * 2 //
    ],
];

$testDates = [
    "2024-01-01 00:00:00",  // Trước khi mua
    "2024-01-02 00:00:00",  // Giữa thời gian sử dụng
    "2024-01-03 00:00:00",  // Giữa thời gian sử dụng
    "2024-01-04 00:00:00",  // Giữa thời gian sử dụng
    "2024-01-05 00:00:00",  // Giữa thời gian sử dụng
    "2024-01-06 00:00:00",  // Giữa thời gian sử dụng
    "2024-01-07 00:00:00",  // Giữa thời gian sử dụng
    "2024-01-08 00:00:00",  // Giữa thời gian sử dụng
    "2024-01-09 00:00:00",  // Giữa thời gian sử dụng
    "2024-01-10 00:00:00",  // Giữa thời gian sử dụng
    "2024-01-11 00:00:00",  // Giữa thời gian sử dụng
    "2024-01-12 00:00:00",  // Giữa thời gian sử dụng
    "2024-01-15 00:00:00",  // Giữa thời gian sử dụng
];

foreach ($testDates as $date) {
    $remaining = calculateRemainingSeconds($orders, $date);
    $nday =  $remaining / 3600 / 24;
    echo "<br> $date: $nday/ " . $remaining  ;

}


    */


    static function calculateRemainingSeconds($orders, $checkDate) {
        if (empty($orders)) {
            return [
                 0,
                 0
            ];
        }
        // Sắp xếp các đơn hàng theo thời gian
        usort($orders, function($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        $checkDateTime = strtotime($checkDate);
        $lastExpiryDate = null;

        foreach ($orders as $order) {
            $orderDate = strtotime($order['date']);

            // Bỏ qua các đơn hàng sau thời điểm kiểm tra
            if ($orderDate > $checkDateTime) {
                continue;
            }

            // Nếu là đơn hàng đầu tiên hoặc đơn hàng sau khi hết hạn gói trước
            if ($lastExpiryDate === null || $orderDate >= $lastExpiryDate) {
                $lastExpiryDate = $orderDate + $order['nSeconds'];
            } else {
                $lastExpiryDate = $lastExpiryDate + $order['nSeconds'];
            }
        }

//        echo "<br/>\n lastExpiryDate = " . nowyh($lastExpiryDate);

        // Nếu thời điểm kiểm tra nằm trong thời hạn sử dụng
        if ($checkDateTime < $lastExpiryDate) {
            $ret = max(0, $lastExpiryDate - $checkDateTime);
            return [
                $ret,
                nowyh($lastExpiryDate)
            ];
        }

        return [
            0,
            nowyh($lastExpiryDate)
        ];
    }
}
