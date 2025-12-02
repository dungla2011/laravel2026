<?php

namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Laravel\Database\TraitModelExtra;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonitorUser
{
    /**
     * Tính quota và thời gian hết hạn dựa trên currentTime
     *
     * @param int $uid User ID
     * @param int $currentTime Timestamp hiện tại
     * @return array ['quota' => int, 'expired_date' => timestamp, 'active_subscriptions' => array]
     */
    public static function getQuotaAndExpiry($uid, $currentTime = null)
    {
        if(!$currentTime)
            $currentTime = time();
        // Lấy tất cả subscription periods
        $subscriptions = self::getAllQuotaAndExpiryBuyed($uid);

        $maxQuota = 0;
        $latestExpiry = 0;
        $activeSubscriptions = [];

        foreach ($subscriptions as $subscription) {
            $fromTime = $subscription['from_time'];
            $toTime = $subscription['to_time'];
            $quota = $subscription['quota'];

            // Kiểm tra subscription có active tại currentTime không
            if ($currentTime >= $fromTime && $currentTime <= $toTime) {
                $activeSubscriptions[] = $subscription;

                // Lấy quota MAX (không cộng dồn)
                if ($quota > $maxQuota) {
                    $maxQuota = $quota;
                }

                // Tìm thời gian hết hạn muộn nhất
                if ($toTime > $latestExpiry) {
                    $latestExpiry = $toTime;
                }
            }
        }

        return [
            'quota' => $maxQuota,
            'expired_date' => $latestExpiry,
            'expired_date_formatted' => $latestExpiry ? date('Y-m-d H:i:s', $latestExpiry) : null,
            'active_subscriptions' => $activeSubscriptions,
            'is_active' => $maxQuota > 0
        ];
    }

    static function countAlertType($uid, $alertType)
    {
        $count = MonitorConfig::where('user_id', $uid)
            ->where('alert_type', $alertType)
            ->count();

        return $count;
    }

    public static function getAllQuotaAndExpiryBuyed($uid)
    {
        //Lấy tất cả các đơn mua của user
        $mOrders = OrderItem::where('user_id', $uid)->get();

        $mTimeSubscriptions = [];
        foreach ($mOrders AS $oneOrder){
            $productId = $oneOrder->product_id;
            //Lấy ra sản phẩm
//            echo "<br/>\n PID = $productId";
            if(!$product = Product::find($productId))
                continue;
//            echo "<br/>\n PIDx";
            //Lấy ra product attribute
            $mA = ProductAttribute::where("product_id", $productId)->get();

//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($mA->toArray());
//            echo "</pre>";

            $toTime = '';
            $limitTime = $limitNode = 0;
            foreach ($mA AS $oneA){
                if($oneA->attribute_name == 'limit_node'){
                    $limitNode = $oneA->attribute_value; //Số node
                }
                if($oneA->attribute_name =='limit_time') {
                    $limitTime = $oneA->attribute_value; //Số ngày
                    if (!$oneOrder->quantity || !is_numeric($oneOrder->quantity))
                        $oneOrder->quantity = 1;


                    if (strstr($limitTime, 'd')) {
                        $nDay = str_replace('d', '', $limitTime);
                        if (!$nDay || !is_numeric($nDay))
                            continue;
                        $nDay *= $oneOrder->quantity;
                        $toTime = strtotime("+$nDay days", $oneOrder->created_at->timestamp);
                    } elseif (strstr($limitTime, 'm')) {
                        $nMonth = str_replace('m', '', $limitTime);
                        if (!$nMonth || !is_numeric($nMonth))
                            continue;
                        $nMonth *= $oneOrder->quantity;
                        $toTime = strtotime("+$nMonth months", $oneOrder->created_at->timestamp);
                    } elseif (strstr($limitTime, 'y')) {
                        $nYear = str_replace('y', '', $limitTime);
                        if (!$nYear || !is_numeric($nYear))
                            continue;
                        $nYear *= $oneOrder->quantity;
                        $toTime = strtotime("+$nYear years", $oneOrder->created_at->timestamp);
                    } else {
                        continue;
                    }
                }
            }
            if($toTime) {
                $mTimeSubscriptions[] = [
                    'from_time' => $oneOrder->created_at->timestamp,
                    'from_time_formatted' => $oneOrder->created_at->format('Y-m-d H:i:s'),
                    'to_time' => $toTime,
                    'to_time_formatted' => date('Y-m-d H:i:s', $toTime) ,
                    'quota' => $limitNode, 'limit_time' => $limitTime, 'quantity'=>$oneOrder->quantity];
            }
        }

        return $mTimeSubscriptions;
    }

    public static function getCurrentNumberMonitorAllow($uid)
    {
        if(!$uid)
            return DEF_MONITOR_DEFAULT_FREE_QUOTA;

        $mm = self::getQuotaAndExpiry($uid);
        $nAllow = $mm['quota'] ?? 0;
        if(!$nAllow)
            $nAllow = DEF_MONITOR_DEFAULT_FREE_QUOTA;
        //Hết hạn chưa
        if($mm['expired_date'] && $mm['expired_date'] < time())
            $nAllow = DEF_MONITOR_DEFAULT_FREE_QUOTA;
        if(!$nAllow)
            $nAllow = DEF_MONITOR_DEFAULT_FREE_QUOTA;
        return $nAllow;
    }


}


