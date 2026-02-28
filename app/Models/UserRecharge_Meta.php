<?php

namespace App\Models;

use App\Components\Helper1;
use LadLib\Common\cstring2;
use LadLib\Common\Database\MetaOfTableInDb;
use LadLib\Common\UrlHelper1;

class UserRecharge_Meta extends MetaOfTableInDb
{
    public static $modelClass = UserRecharge::class;
    public static $modelName = 'UserRecharge';

//    public static $disableAddItem = 1;
//    public static $disableSaveAllButton = 1;



    public function getHardCodeMetaObj($field) {

        $objMeta = new MetaOfTableInDb();
        if($field == 'log' || $field == 'note' || $field == 'comment' ) {
            $objMeta->dataType = DEF_DATA_TYPE_TEXT_AREA;
        }
        if ($field == 'image_list')
            $objMeta->dataType = DEF_DATA_TYPE_IS_MULTI_IMAGE_BROWSE;

        if ($field == 'user_id') {
            $objMeta->join_api_field = 'email';
            //            $objMeta->join_func = 'joinUserEmailUserId';
            $objMeta->join_api = '/api/user/search';
        }

//        if(!$objMeta->dataType)
//            return null;

        return $objMeta;
    }

    public function getFullSearchJoinField()
    {
        if(Helper1::isAdminModule())
            return [
                'users.email'  => "like",
            ];
    }

    function getMapJoinFieldAlias()
    {
        return [
            '_email' => 'users.email',
        ];
    }
    function getSqlOrJoinExtraIndex(\Illuminate\Database\Eloquent\Builder &$x = null, $getSelect = 0)
    {
        if(Helper1::isAdminModule())
            return $x->leftJoin('users', 'user_id', '=', 'users.id')
                ->addSelect([
                    'users.email AS _email',
                ]);
        else{
//            die(UrlHelper1::getFullUrl());
            if(!str_contains(UrlHelper1::getFullUrl() , 'soby_'))
                return $x->orderBy("created_at", "DESC");
        }
    }

    function _image_list($obj, $val, $field){
        return Helper1::imageShow1($obj, $val, $field);
    }



    function _amount($obj, $val, $field){


    }

    public function setDefaultValue($field)
    {
        if ($field == 'status') {
            return 'completed';
        }
    }

    public static function getCoreFields()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Người dùng',
            'amount' => 'Số tiền (đ)',
            'payment_method' => 'Hình thức',
            'status' => 'Trạng thái',
            'paid_at' => 'Thanh toán lúc',
            'completed_at' => 'Hoàn tất lúc',
        ];
    }

    function _invoice_number($obj = null, $v2 = null, $v3 = null)
    {

        $retPdf = "";
        if($v2){
            $sid = getSiteIDByDomain();

            $file = "/var/glx/upload_file_glx/user_files/siteid_$sid/invoice_files/$v2.pdf";
//            echo "\n xxx $file";

            if(file_exists("/var/glx/upload_file_glx/user_files/siteid_$sid/invoice_files/$v2.pdf")){
                $linkPdf = "/_site/hosting_site/invoices_glx.php?action=view_pdf&file=$v2";
                $retPdf .= "<a href='$linkPdf' target='_blank' class='m-2'> View PDF </a>";
            }

        }

        $userId =  $obj->user_id;

        $pn = PartnerInfo::where('user_id', $userId)->first();

        $taxCode = $pn->tax_code ?? '';

        $amount = str_replace(",", "", number_format($obj->amount, 0, ''));

        if(Helper1::isAdminModule())
            return "$retPdf <div class='m-2'>
<a target='_blank' href='/_site/hosting_site/invoices_glx.php?tax_code=$taxCode&amount=$amount'>
<button class='btn btn-outline-primary btn-sm get_tax_info' data-ammont='$amount' data-code-tax='$taxCode' type='button'>
GetInvoice </button>
</a>
</div>";

        return $retPdf;

    }

    public function extraContentIndex1($v1 = null, $v2 = null, $v3 = null)
    {

        $uid = getCurrentUserId();

        $mm = UserRecharge::where("user_id", $uid)->get();
        $tt = 0;
        foreach ($mm AS $one){
            $tt += $one->amount;
        }
        $ttFormat = number_format($tt, 0, ',', '.');
        $notVAT = $tt / 1.1;
        $notVATFormat = number_format($notVAT, 0, ',', '.');
        $vat = $tt - $notVAT;
        $vatFormat = number_format($vat, 0, ',', '.');

        $str = '';
        if(isSupperAdmin_() && isAdminLrv_()) {
            $str = "/var/www/html/public/_site/hosting_site/download-invoices.php <a href='/_site/hosting_site/list-invoices-from-api.php' target='_blank'> [LIST API] </a>";

        }
        $text = cstring2::toTienVietNamString3($tt);
        echo " <div class='p-2 m-2 bg-light' style='font-size: 90%; border: 1px solid #ccc'> Tổng đã thanh toán: <b> $notVATFormat + $vatFormat (VAT) = $ttFormat VND </b> ($text) <br> $str </div>";

        ?>




<?php
    }

    public function extraCssInclude()
    {
?>

        <style>
            .one_link_ {
                display:block;
                margin: 3px 10px!important;
            }
            .divTable2Cell > textarea {
                min-width: 300px;
                min-height: 70px!important;
            }
            input.input_value_to_post.image_list {
                display: none;
            }
        </style>
<?php
    }

    function _user_id($obj, $val, $field)
    {
        return  User_Meta::search_user_email($obj, $val, $field);
//        $user = User::find($val);
//        if($user)
//            return " <div style='font-size: small; margin-left: 10px'> $user->email </div> ";
    }




    public static function _name($item, $typeGet = '')
    {
        if (!$item) return '';

        $statusColor = match($item->status) {
            'completed' => '✅ Hoàn tất',
            'pending' => '⏳ Chờ',
            'processing' => '⚙️ Đang xử lý',
            'failed' => '❌ Thất bại',
            'cancelled' => '🚫 Hủy',
            default => $item->status
        };

        $completedInfo = $item->completed_at ? 'Hoàn tất: ' . $item->completed_at : 'Chưa hoàn tất';

        return <<<HTML
            <div style="padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                <div><strong>Recharge #{$item->id}</strong> - User #{$item->user_id}</div>
                <table style="width: 100%; margin-top: 8px; border-collapse: collapse;">
                    <tr style="background: #f5f5f5;">
                        <td style="padding: 8px; border: 1px solid #ddd;"><strong>Số tiền</strong></td>
                        <td style="padding: 8px; border: 1px solid #ddd;"><strong>Phương thức</strong></td>
                        <td style="padding: 8px; border: 1px solid #ddd;"><strong>Trạng thái</strong></td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd;"><strong>{$item->amount}</strong></td>
                        <td style="padding: 8px; border: 1px solid #ddd;">{$item->payment_method}</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">$statusColor</td>
                    </tr>
                </table>
                <div style="margin-top: 8px; font-size: 12px;">
                    {$completedInfo}
                </div>
            </div>
        HTML;
    }
}
