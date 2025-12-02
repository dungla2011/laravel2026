<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;
use LadLib\Common\UrlHelper1;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Meta;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class Telesale_Meta extends OrderInfo_Meta
{
    protected static $api_url_admin = '/api/telesale';

    protected static $web_url_admin = '/admin/telesale';

    protected static $api_url_member = '/api/member-telesale';

    protected static $web_url_member = '/member/telesale';

    //public static $folderParentClass = TelesaleFolderTbl::class;
    public static $modelClass = Telesale::class;

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();
        if ($field == 'status') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;

            return $objMeta;
        }
        if ($field == 'print_status') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;

            return $objMeta;
        }
        if ($field == 'api_key_ship') {
            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;

            return $objMeta;
        }

        return parent::getHardCodeMetaObj($field);
    }

    public function _api_key_ship($obj, $val, $field)
    {
        //        return null;
        $mm = PartnerInfo::all();

        $mr = [];
        $mr[0] = '-KeyApi-';
        foreach ($mm as $x) {
            //            dump($x);
            $mr[$x->id] = "$x->id - ".$x->name;
        }

        //        if(!$val)
        //            return null;

        return $mr;
    }

    public function _state1($obj)
    {

        $mm = [
            0 => '---',
            1 => 'Đã chốt đơn',
            2 => 'Không nghe máy',
            3 => 'Cần gọi lại',
            4 => 'Hủy đơn',
            5 => 'Lý do khác',
        ];

        return $mm;

    }

    public static function getDataHtmlToPrint($idList)
    {

        $mDataPrint = Telesale::whereIn('id', $idList)->latest()->get();

        if (! $mDataPrint) {
            loi('Not found data to print: '.json_encode($idList));
        }
        $ret1 = '';
        $cc = 0;
        foreach ($mDataPrint as $dt0) {
            $cc++;
            $idf = $dt0['id'];

            $dt = Telesale::find($idf);
            if (! $dt) {
                continue;
            }

            $mt = new Telesale_Meta();
            $productText = $mt->_service_require($dt, 'get_text_to_ship');
            if (strlen($productText) > 85) {
                $productText = mb_substr($productText, 0, 85).'...';
            }

            //            dump($dt);

            $ods = OrderShip::where('order_id', $idf)->first();
            if (! $ods) {
                loi("Không thấy đơn trong bảng Ship? order_id = $idf");

                continue;
            }

            $addressStr = '';
            if ($dt->to_address) {
                $dc = json_decode($dt->to_address);
                $addressStr .= $dc->detail.', ';
                if (isset($dc->l3)) {
                    $addressStr .= $dc->l3.', ';
                }
                if (isset($dc->l2)) {
                    $addressStr .= $dc->l2.', ';
                }
                if (isset($dc->l1)) {
                    $addressStr .= $dc->l1;
                }
            }

            $dateCreate = nowyh(strtotime($dt->created_at));

            //            <svg class="barcode"
            //  jsbarcode-format="upc"
            //  jsbarcode-value="123456789012"
            //  jsbarcode-textmargin="0"
            //  jsbarcode-fontoptions="bold">
            //</svg>
            //
            $ret2 = "<div class='mainPrints' style=''>
<div style='position: absolute;display: flex; bottom: 5px; right: 5px; border: 1px solid gray; width: 30px; height: 30px;  align-items: center;
    justify-content: center; border-radius: 50%'> <b>$cc</b> </div>
<table class='glx08' style='font-size: small'>
<tr>
<td>
Mã đơn <br> ($idf)</td>
<td style='padding: 0px'>

<svg data-tracking-id='$ods->remote_tracking_id' class='bar_code'></svg>
<div style='float: right; text-align: center'>
<div style='padding: 5px; font-size: smaller; text-align: right; line-height: 13px'>Ngày Tạo<br> $dateCreate</div>
<b style='padding: 5px;'>Thu hộ: $dt->money (VNĐ) </b>
</div>
</td>
</tr>
<tr>
<td style='min-width: 80px'>Khách hàng</td>
<td> $dt->name </td>
</tr>
<tr>
<td>Điện thoại</td>
<td> $dt->phone_request </td>
</tr>
<tr>
<td>Địa chỉ</td>
<td> $addressStr </td>
</tr>

<tr>
<td>Sản phẩm</td>
<td> $productText </td>
</tr>
<tr>
<td>Shop/Cửa hàng</td>
<td> ... </td>
</tr>
<tr>
<td>Khối lượng</td>
<td> ... </td>
</tr>

</table>
</div>";

            $ret1 .= $ret2;
        }

        return $ret1;
    }

    public function extraJsInclude()
    {
        ?>

        <style>
            select[data-field=api_key_ship] {
                width: 60px!important;
                min-width: 20px!important;
            }
        </style>

        <?php
        parent::extraJsInclude(); // TODO: Change the autogenerated stub
    }

    public function extraContentIndex1($v1 = null, $v2 = null, $v3 = null)
    {
        $clink = UrlHelper1::getFullUrl();
        if (UrlHelper1::getUriWithoutParam() != '/member/telesale') {
            return;
        }

        //        dump($v1->toArray()['data']);

        $mDataPrint = $v1->toArray()['data'];

        $ret1 = '';
        //        $ret1 = "<div style='display: none' class='all_van_don_ship' id='all_van_don_ship'>";
        //        foreach ($mDataPrint AS $dt0){
        //
        //
        //            $idf = $dt0['id'];
        //
        //            $dt = Telesale::find($idf);
        //            if(!$dt)
        //                continue;
        //
        //            $mt = new Telesale_Meta();
        //            $productText = $mt->_service_require($dt,'get_text_to_ship');
        //            if(strlen($productText) > 85)
        //                $productText = mb_substr($productText, 0, 85) .'...';
        //
        ////            dump($dt);
        //
        //
        //            $ods = OrderShip::where("order_id", $idf)->first();
        //            if(!$ods)
        //                continue;
        //
        //            $addressStr = "";
        //            if($dt->to_address){
        //                $dc = json_decode($dt->to_address);
        //                $addressStr .= $dc->detail. ', ';;
        //                if(isset($dc->l3))
        //                    $addressStr .= $dc->l3 . ', ';
        //                if(isset($dc->l2))
        //                    $addressStr .= $dc->l2 . ', ';
        //                if(isset($dc->l1))
        //                    $addressStr .= $dc->l1;
        //            }
        //
        //            $dateCreate = nowyh(strtotime($dt->created_at));
        //
        ////            <svg class="barcode"
        ////  jsbarcode-format="upc"
        ////  jsbarcode-value="123456789012"
        ////  jsbarcode-textmargin="0"
        ////  jsbarcode-fontoptions="bold">
        ////</svg>
        ////
        //            $ret2 = "<div class='mainPrints' style=''>
        //<table class='glx08' style='font-size: small'>
        //<tr>
        //<td>Mã đơn <br> ($idf)</td>
        //<td style='padding: 0px'>
        //
        //<svg data-tracking-id='$ods->remote_tracking_id' class='bar_code'></svg>
        //<div style='float: right; text-align: center'>
        //<div style='padding: 5px; font-size: smaller; text-align: right; line-height: 13px'>Ngày Tạo<br> $dateCreate</div>
        //<b style='padding: 5px;'>Thu hộ: $dt->money (VNĐ) </b>
        //</div>
        //</td>
        //</tr>
        //<tr>
        //<td style='min-width: 80px'>Khách hàng</td>
        //<td> $dt->name </td>
        //</tr>
        //<tr>
        //<td>Điện thoại</td>
        //<td> $dt->phone_request </td>
        //</tr>
        //<tr>
        //<td>Địa chỉ</td>
        //<td> $addressStr </td>
        //</tr>
        //
        //<tr>
        //<td>Sản phẩm</td>
        //<td> $productText </td>
        //</tr>
        //<tr>
        //<td>Shop/Cửa hàng</td>
        //<td> ... </td>
        //</tr>
        //<tr>
        //<td>Khối lượng</td>
        //<td> ... </td>
        //</tr>
        //</table>
        //</div>";
        //
        //
        //        $ret1 .= $ret2;
        //        }
        //        $ret1 .= "</div>";

        echo $ret1;
        //$txt = json_encode($ret1);
        //echo "<script>var jsRetPrint={$txt};</script>";

        ?>


        <div data-code-pos='ppp16869057560531' style="padding-bottom: 10px; border-bottom: 1px solid #ccc; margin: 2px 8px 10px 8px">
            <a href="/member/telesale?seoby_s22=ne&seby_s22=2">
            <button type="button" class="btn <?php
            if (strstr($clink, 'seoby_s22=ne') && strstr($clink, 'seby_s22=2')) {
                echo 'btn-warning';
            } else {
                echo 'btn-default ';
            }
        ?> "
                    style=""><i class="fa fa-plus"></i>  Đơn cần xử lý</button>
            </a>
            <a href="/member/telesale?seby_s22=2">
            <button type="button" class="btn <?php
            $donHt = 0;
        if (strstr($clink, 'seoby_s22=ne') === false && strstr($clink, 'seby_s22=2')) {
            $donHt = 1;
            echo 'btn-info';
        } else {
            echo 'btn-default ';
        }
        ?> "
                    style=""> <i class="fa fa-save"></i> Đơn đã Hoàn thành của bạn</button>
            </a>
            <a href="/member/telesale?thongke=1">
            <button type="button" class="btn
            <?php
        $thongKe = 0;
        if (strstr($clink, 'thongke=1')) {
            $thongKe = 1;
            echo 'btn-primary';
            $this->ignoreIndexTable = 1;
        } else {
            echo 'btn-default ';
        } ?> "
                    style="">  <i class="fa fa-tree"></i>  Thống kê đơn hiện tại
            </button>
            </a>

            <?php
        if ($donHt) {
            ?>
                <button type="button" class="btn btn-primary" id="print_orders"> <i class="fa fa-print"></i> In đơn </button>
                <a href="<?php
            echo UrlHelper1::setUrlParamThisUrl('seby_s27', 0);
            ?>">
                <button type="button" class="btn <?php if (strstr($clink, 'seby_s27=0')) {
                    echo 'btn-warning';
                } else {
                    echo 'btn-default';
                } ?>"> <i class="fa fa-print"></i> Đơn chưa in </button>
                </a>
                <?php
        }
        ?>
        </div>

        <?php

        if ($thongKe) {
            echo "<div class='jumbotron'>
<b>Thống kê Telesale toàn Hệ thống </b>
<br>
- Tổng số đơn chưa xử lý hiện tại: 150 đơn
<br>
- Số đơn đã được xử lý hôm nay: ...
<br>
- Số đơn hủy hôm nay: ...
 </div>";
        }

        ?>

<?php
    }

    //...
}
