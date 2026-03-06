<?php

namespace App\Models;

use App\Components\Helper1;
use LadLib\Common\Database\MetaOfTableInDb;
use LadLib\Common\UrlHelper1;

class VpsUsage_Meta extends MetaOfTableInDb
{

    public static $folderParentClass = VpsUsage::class;

    public static $modelClass = VpsUsage::class;

    public function getHardCodeMetaObj($field) {

        $objMeta = new MetaOfTableInDb();
        if($field == 'power_state'){
            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;
        }



//        if(!$objMeta->dataType)
//            return null;

        return $objMeta;
    }

    function getMapJoinFieldAlias()
    {
        return [
            '_email' => 'users.email',
        ];
    }
    function getSqlOrJoinExtraIndex(\Illuminate\Database\Eloquent\Builder &$x = null, $getSelect = 0)
    {
        return $x->leftJoin('vps_instances', 'instance_id', '=', 'vps_instances.id')
            ->leftJoin('users', 'vps_instances.user_id', '=', 'users.id')
            ->addSelect([
                'users.email AS _email',
            ]);
    }


    function _name($obj, $val, $field)
    {
        $instance = "<a target='_blank' href='/admin/vps-instance/edit/$obj->instance_id'> Instance </a>";
        return "$instance | $obj->_email";
    }
    function _email($obj, $val, $field)
    {
        return "$val";
    }

    public function getFullSearchJoinField()
    {
        return [
            'users.email'  => "like",
            'vps_usages.list_ip_address'  => "like",
        ];
    }

    function _user_id($obj, $val)
    {
//        $user = User::find($val);
//        if($user)
//            return " <div style='font-size: small; margin-left: 10px'> $user->email </div> ";
    }

    public function extraCssInclude()
    {
        ?>

        <style>
            .input_value_to_post.name{
                min-width: 250px;
            }

        </style>

        <style>
            .input_value_to_post.readonly.list_ip_address{
                display: none;
            }
            .input_value_to_post.price_config , .input_value_to_post.calculated_fee{
                display: none;

            }
            div.calculated_fee{
                min-width: 70px;
            }
            .input_value_to_post.readonly.power_state{
                display: none;
            }
        </style>
<?php

    }

    function _price_config($obj, $val, $field)
    {
        if(!$obj || !$val)
            return;

        $mm = json_decode($val);
        $ret = '';
        $cc = 0;
        if(is_array($mm ) || is_object($mm ))
        foreach ($mm AS $key=>$val){
            $cc++;
            $ret .= "$val/";
            if($cc == 3)
                $ret.="<br>";
        }
        return "<div class='div_td'> $ret </div>";
    }

    function _list_ip_address($obj, $val, $field){
        $val = str_replace(",", "<br>", $val);
        return "<div style='color: green; font-size: smaller; margin-left: 5px'>$val </div>";
    }

    function _power_state($obj, $val, $field){

        $mm = [
            "" => "-change-",
            "POWERED_OFF" => "POWERED_OFF",
            "POWERED_ON" => "POWERED_ON",
            "OLD_CONFIG" => "OLD_CONFIG",
        ];
        return $mm;

//        $val1 = "OFF";
//        if(str_contains($val, "_ON")){
//            $color = "green";
//            $val1 = "ON";
//        }
//        else
//            $color = "red";
//
//        return "<div style='color: $color; font-size: smaller; margin-left: 5px; text-align: center'>$val1 </div>";
    }


    function _image_list($obj, $val, $field){
        return Helper1::imageShow1($obj, $val, $field);
    }


    function _lastest_time_the_same($obj, $val, $field)
    {
        //Delta Time with time()
        $dtime = time() - strtotime($val);
        $minute = floor($dtime / 60);
        $hour = floor($minute / 60);
        $day = floor($hour / 24);

        // Determine color based on freshness
        if ($minute < 15) {
            $color = "green";
        } elseif ($hour < 24) {
            $color = "orange";
        } else {
            $color = "red";
        }

        // Format time display
        if ($minute < 60) {
            $display = "$minute phút trước";
        } elseif ($hour < 24) {
            $display = "$hour giờ trước";
        } else {
            $display = "$day ngày trước";
        }

        return "<span style='color: $color; font-size: smaller; margin-left: 5px'>($display)</span>";
    }

    function _last_found_ip($obj, $val, $field)
    {
        //Delta Time with time()
        $dtime = time() - strtotime($val);
        $minute = floor($dtime / 60);
        $hour = floor($minute / 60);
        $day = floor($hour / 24);

        // Determine color based on freshness
        if ($minute < 5) {
            $color = "green";
        } elseif ($hour < 24) {
            $color = "orange";
        } else {
            $color = "red";
        }

        // Format time display
        if ($minute < 60) {
            $display = "$minute phút trước";
        } elseif ($hour < 24) {
            $display = "$hour giờ trước";
        } else{
            $display = "$day ngày trước";
        }

        return "<span style='color: $color; font-size: smaller; margin-left: 5px'>($display)</span>";
    }

    public function extraContentIndexButton1($v1 = null, $v2 = null, $v3 = null)
    {
       ?>

       <a href="/tool/site/galaxy/join_vps_usage_the_same_config.php" target="_blank">

        <button type="button" class="btn btn-sm btn-info float-right mt-2" id="join_all_the_same_vps_time"> JOIN ALL </button>
       </a>

<?php
    }

    function _calculated_fee($obj, $val, $field)
    {
        if(!str_contains(UrlHelper1::getFullUrl(), 'edit/')){
//            return number_format($val,1) . " K ";
        }

        // Get pricing config
        $priceConfig = json_decode($obj->price_config, true);

        // Calculate duration in minutes
        $createdAt = strtotime($obj->created_at);
        $lastestTime = strtotime($obj->lastest_time_the_same ?? $obj->created_at);
        $durationMinutes = max(0, floor(($lastestTime - $createdAt) / 60));

        // Determine CPU and RAM for fee calculation (0 if powered off)
        $feeCpu = ($obj->power_state === 'POWERED_OFF') ? 0 : $obj->cpu;
        $feeRam = ($obj->power_state === 'POWERED_OFF') ? 0 : $obj->ram_gb;

        // Calculate daily fee breakdown
        $dailyCpuFee = (($priceConfig['n_cpu_core_price'] ?? 0) / 30) * $feeCpu;
        $dailyRamFee = (($priceConfig['n_ram_gb_price'] ?? 0) / 30) * $feeRam;
        $dailyDiskFee = (($priceConfig['n_gb_disk_price'] ?? 0) / 30) * $obj->disk_gb;

        // Count chargeable IPs (free local IPs + 1 free internet IP)
        $chargeableIpCount = \App\Services\VpsUsageFeeService::countChargeableIPs($obj->list_ip_address);
        $dailyIpFee = (($priceConfig['n_ip_address_price'] ?? 0) / 30) * $chargeableIpCount;

        $dailyTotalFee = $dailyCpuFee + $dailyRamFee + $dailyDiskFee + $dailyIpFee;

        // Calculate period fee
        $periodFee = $dailyTotalFee * ($durationMinutes / 1440);
        $periodFee = round($periodFee, 4);

        // Color based on fee amount
        $fee = floatval($val);
        if ($fee == 0) {
            $color = "gray";
        } elseif ($fee < 10) {
            $color = "green";
        } elseif ($fee < 100) {
            $color = "orange";
        } else {
            $color = "red";
        }

        // Build detail table
        $detail = "<table style='font-size: 11px; border-collapse: collapse; margin-top: 5px;'>
            <tr style='background: #f0f0f0;'>
                <td style='border: 1px solid #ddd; padding: 3px;'><b>Hạng mục</b></td>
                <td style='border: 1px solid #ddd; padding: 3px; text-align: right;'><b>Giá/30 ngày</b></td>
                <td style='border: 1px solid #ddd; padding: 3px; text-align: right;'><b>Lượng</b></td>
                <td style='border: 1px solid #ddd; padding: 3px; text-align: right;'><b>Giá/ngày</b></td>
            </tr>
            <tr>
                <td style='border: 1px solid #ddd; padding: 3px;'>CPU ({$feeCpu} core)</td>
                <td style='border: 1px solid #ddd; padding: 3px; text-align: right;'>" . number_format($priceConfig['n_cpu_core_price'] ?? 0, 0) . "K</td>
                <td style='border: 1px solid #ddd; padding: 3px; text-align: right;'>{$feeCpu}</td>
                <td style='border: 1px solid #ddd; padding: 3px; text-align: right;'>" . number_format($dailyCpuFee, 2) . "K</td>
            </tr>
            <tr>
                <td style='border: 1px solid #ddd; padding: 3px;'>RAM ({$feeRam} GB)</td>
                <td style='border: 1px solid #ddd; padding: 3px; text-align: right;'>" . number_format($priceConfig['n_ram_gb_price'] ?? 0, 0) . "K</td>
                <td style='border: 1px solid #ddd; padding: 3px; text-align: right;'>{$feeRam}</td>
                <td style='border: 1px solid #ddd; padding: 3px; text-align: right;'>" . number_format($dailyRamFee, 2) . "K</td>
            </tr>
            <tr>
                <td style='border: 1px solid #ddd; padding: 3px;'>Disk ({$obj->disk_gb} GB)</td>
                <td style='border: 1px solid #ddd; padding: 3px; text-align: right;'>" . number_format($priceConfig['n_gb_disk_price'] ?? 0, 0) . "K</td>
                <td style='border: 1px solid #ddd; padding: 3px; text-align: right;'>{$obj->disk_gb}</td>
                <td style='border: 1px solid #ddd; padding: 3px; text-align: right;'>" . number_format($dailyDiskFee, 2) . "K</td>
            </tr>
            <tr>
                <td style='border: 1px solid #ddd; padding: 3px;'>IP ({$chargeableIpCount} tính phí)</td>
                <td style='border: 1px solid #ddd; padding: 3px; text-align: right;'>" . number_format($priceConfig['n_ip_address_price'] ?? 0, 0) . "K</td>
                <td style='border: 1px solid #ddd; padding: 3px; text-align: right;'>{$chargeableIpCount}</td>
                <td style='border: 1px solid #ddd; padding: 3px; text-align: right;'>" . number_format($dailyIpFee, 2) . "K</td>
            </tr>
            <tr style='background: #e8f4f8; font-weight: bold;'>
                <td style='border: 1px solid #ddd; padding: 3px;'>Tổng/ngày</td>
                <td colspan='2' style='border: 1px solid #ddd; padding: 3px;'></td>
                <td style='border: 1px solid #ddd; padding: 3px; text-align: right;'>" . number_format($dailyTotalFee, 2) . "K</td>
            </tr>
            <tr style='background: #fff3cd;'>
                <td style='border: 1px solid #ddd; padding: 3px;' colspan='2'><b>Thời gian: {$durationMinutes} phút</b></td>
                <td style='border: 1px solid #ddd; padding: 3px; text-align: right;' colspan='2' style='text-align: right;'><b>Phí: " . number_format($periodFee, 4) . "K</b></td>
            </tr>
        </table>";
        $ret = "<div style='color: $color;  padding: 5px'>" . number_format(round($periodFee), 0, '.') . " K</div>";
        if(!str_contains(UrlHelper1::getFullUrl(), 'edit/')){
            return $ret;
        }
        return   $ret . $detail;
    }



}

