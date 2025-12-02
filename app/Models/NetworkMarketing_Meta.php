<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class NetworkMarketing_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/network-marketing';

    protected static $web_url_admin = '/admin/network-marketing';

    protected static $api_url_member = '/api/member-network-marketing';

    protected static $web_url_member = '/member/network-marketing';

    public static $folderParentClass = NetworkMarketing::class;

    public static $modelClass = NetworkMarketing::class;

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();
        if ($field == 'status') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //NetworkMarketing edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    public static function getCookieNameShb()
    {
        return 'shb_link_acc_2023';
    }

    public static function getCookieNameAbc()
    {
        return 'abc_link_acc_2023';
    }

    public function _user_id($obj, $val)
    {
        $user = User::find($val);
        if ($user) {
            return " <div style='font-size: small; padding: 3px'> $user->email </div> ";
        }
    }

    public function _parent_id($obj, $val, $field)
    {
        if (! $val) {
            return null;
        }
        $user = User::find($obj->user_id);
        if ($user) {
            return " <div style='font-size: small; padding: 3px'> $user->email </div> ";
        }
    }

    public static function tinhProfitNetwork($mmData)
    {
        //Tính tất cả tree deep của từng người
        $mmData2 = [];
        foreach ($mmData as $o1) {
            $mmData2[] = json_decode(json_encode($o1));
        }

        $retAll = [];
        foreach ($mmData2 as $o1) {
            $o2 = json_decode(json_encode($o1));
            $lv = 0;
            $ret = \App\Models\GiaPha_Meta::getTreeDeepBelongObjInATree($o2, $lv, $mmData2);
            $retAll[$o2->id.' - '.$o2->name] = json_decode(json_encode($ret));
        }

        $maxLvBonus = 5;
        $payOne = 10;
        $prePay = 0;
        $bonusAllOne = 70;
        $totalMoneyBonux = 0;
        echo "<br/>\n<button onclick='toggleDetail1()'> Open Detail </button>";
        echo "<br/>\n<div class='profit_one_net' style='display: none'>";
        foreach ($retAll as $nameAndId => $mx) {
            $count = 0;
            if ($mx) {
                foreach ($mx as $x2) {
                    if ($x2->level <= $maxLvBonus) {
                        $count++;
                    }
                }
            }

            $ttChild = count($mx);

            $money = $count * $payOne;
            $totalMoneyBonux += $money;
            echo "<br/>\n $nameAndId (nChild = $ttChild) => $money";
        }
        echo "<br/>\n<button onclick='toggleDetail1()'> Open Detail </button>";
        echo "<br/>\n</div>";

        $nMmember = count($mmData);
        $totalGain = $nMmember * $bonusAllOne;
        $totalMoneyPay1 = $nMmember * $prePay;

        echo "<br/>\n - Config: prePay = $prePay, payOne = $payOne, bonusAllOne = $bonusAllOne, maxLevel = $maxLvBonus";

        echo "<br/>\n - Total Pay = $totalMoneyBonux + $totalMoneyPay1";

        echo "<br/>\n - Total Member: ".$nMmember;
        echo "<br/>\n - Total Gain = $nMmember * $bonusAllOne = ".($nMmember * $bonusAllOne);

        $prof = ($totalGain - $totalMoneyPay1 - $totalMoneyBonux);
        echo "<br/>\n - Profit = $totalGain - $totalMoneyPay1 (Prepay) - $totalMoneyBonux = $prof / ".number_format($prof * 100 / $totalGain).'%';

        ?>

        <script>
            function toggleDetail1(){
                $(".profit_one_net").toggle();
            }
        </script>
<?php
    }

    //...
}
