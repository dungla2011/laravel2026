<?php

namespace App\Models;

use App\Components\Helper1;
use Carbon\Carbon;
use Google\Rpc\Help;
use Illuminate\Support\Facades\DB;
use LadLib\Common\Database\MetaOfTableInDb;
use LadLib\Common\UrlHelper1;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class OrderItem_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/order-item';

    protected static $web_url_admin = '/admin/order-item';

    protected static $api_url_member = '/api/member-order-item';

    protected static $web_url_member = '/member/order-item';

    //public static $folderParentClass = OrderItemFolderTbl::class;
    public static $modelClass = OrderItem::class;

    public static $titleMeta = "Chi tiết đơn mua";

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();

        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //OrderItem edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }
        if ($field == 'user_id') {
            $objMeta->join_api_field = 'email';
            //            $objMeta->join_func = 'joinUserEmailUserId';
            $objMeta->join_api = '/api/user/search';
        }

        return $objMeta;
    }

    public static function isDisableAddItemIndex()
    {
        if(Helper1::isAdminModule())
            return 0;
        return 1;
    }

    public static function isDisableSaveAllItemIndex()
    {
        if(Helper1::isAdminModule())
            return 0;
        return 1;
    }

    public static function isDisableTrashIndex()
    {
        if(Helper1::isAdminModule())
            return 0;
        return 1;
    }

    public static function isDisableMenuBarIndex()
    {
        if(Helper1::isAdminModule())
            return 0;
        return 1;
    }

    /**
     * Lấy mapfiled và class tương ứng để index chỉ query 1 lần, tránh bị multi query
     * @return string[]
     */
    function getMapFieldAndClass()
    {

        return [
            'product_id' => Product::class,
            'user_id' => User::class,
        ];
    }

    public function extraContentIndexButton1($v1 = null, $v2 = null, $v3 = null)
    {
        ?>
        <a href="/buy-vip" class="mt-2" style="float: right">
            <button class="btn btn-sm btn-success">Mua Dịch vụ Tại đây </button>
        </a>
        <?php
    }

    public function _user_id($objData, $value = null, $field = null)
    {
        return User_Meta::search_user_email($objData, $value, $field);
    }

    public function setDefaultValue($field)
    {

        if($field == 'tmp_ngold'){
            return  1500;
        }
        if($field == 'tmp_gold_type'){
            return  -1002;
        }
        if($field == 'note'){
            return  "Admin insert uploader";
        }

    }

    public function extraContentIndex1($v1 = null, $v2 = null, $v3 = null)
    {
        $domain = UrlHelper1::getDomainHostName();

        if(!in_array($domain, ['mytree.vn']))
            return;
        if(!Helper1::isAdminModule())
            return;
        ?>
        <style>
            .input_value_to_post.product_id{
                display: none
            }
        </style>

        <div class="row my-2" style="border-bottom: 1px solid #ccc; padding-bottom: 20px" data-code-pos='ppp17361594374911'>
            <div class="col-sm-12 ml-2">


                <?php



//                if(in_array($domain, ['mytree.vn', '4share.vn']))

                {
                    $monthlyData = DB::table(DB::raw("(SELECT YEAR(created_at) as year, MONTH(created_at) as month,
                    SUM(price) as total_price, COUNT(*) as order_count
                                  FROM order_items
                                  GROUP BY year, month
                                  ORDER BY year DESC, month DESC
                                  LIMIT 5) as sub"))
                        ->orderBy('year', 'desc')
                        ->orderBy('month', 'desc')
                        ->get();

                    foreach ($monthlyData as $data) {
                        $ttP = number_formatvn0($data->total_price);
                        echo " {$data->month}.{$data->year}: <b>{$ttP}</b> ({$data->order_count}) | ";
                    }
                }
                ?>

            </div>
        </div>


<?php




    }


    public function getRandIdListField($field = null)
    {
        return ['id', 'order_id'];

    }

    public function _product_id($obj,  $val, $field)
    {
        if(!$val && $obj->tmp_ngold){
            return "Gói nạp Gold | Số ngày sử dụng: " . ($obj->tmp_ngold / 5) . "   ($obj->tmp_ngold Gold : 5)";
        }

        $x = 1;
        if($pr = (self::$preDataAfterIndex[Product::class][$val] ?? false)){
            $x = 1;
        }
        else {
            $x = 2;
            $pr = Product::find($val);
        }

        if($pr)
            return "<span style='font-size: 80%; padding: 5px; color: dodgerblue'>  $pr->name  </span>";
    }

    function _user_id1($obj, $val)
    {

//        echo "<br/>\n ". User::class;
        if(self::$preDataAfterIndex[User::class][$val] ?? false) {
            $user = self::$preDataAfterIndex[User::class][$val];
//            dump(" found /  $val / " . User::class );
        }
        else{


//            dump(debug_backtrace());


            $user = User::find($val);
//            dump(self::$preDataAfterIndex);
//            dump("NOT found /  $val / " . User::class );
        }

        if ($user) {
            return " <div style='font-size: small; padding: 3px'> $user->email </div> ";
        }
    }

    public function isUseRandId()
    {
        return 1;
    }

    //...
}
