<?php

namespace App\Models;

use App\Components\Helper1;
use App\Http\ControllerApi\EventInfoControllerApi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Common\cstring2;
use LadLib\Common\Database\MetaOfTableInDb;
use LadLib\Common\UrlHelper1;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Meta;

/**
 * ABC123
 * @param null $objData
 */
class EventUserPayment_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = "/api/event-user-payment";
    protected static $web_url_admin = "/admin/event-user-payment";

    protected static $api_url_member = "/api/member-event-user-payment";
    protected static $web_url_member = "/member/event-user-payment";

    //public static $folderParentClass = EventUserPaymentFolderTbl::class;
    public static $modelClass = EventUserPayment::class;
    public static $titleMeta = "Thanh toán sự kiện";

    /**
     * @param $field
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field){

        $objMeta = new MetaOfTableInDb();

        //Riêng Data type của Field, Lấy ra các field datatype mặc định
        //Nếu có thay đổi sẽ SET bên dưới
        $objSetDefault = new MetaOfTableInDb();
        $objSetDefault->setDefaultMetaTypeField($field);

        $objMeta->dataType = $objSetDefault->dataType;

        if($field == 'evidence' || $field == 'comment'){
            $objMeta->dataType = DEF_DATA_TYPE_TEXT_AREA;
        }

        if($field == 'status'){
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if($field == 'tag_list_id'){
            $objMeta->join_api_field = 'name';
//          $objMeta->join_func = 'joinTags';
            //EventUserPayment edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }
        if($field == 'parent_extra' || $field == 'parent_all' ){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_MULTI_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\EventUserPaymentFolderTbl::joinFuncPathNameFullTree';
        }

        if($field == 'parent_id'){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\EventUserPaymentFolderTbl::joinFuncPathNameFullTree';
        }

        //Nếu không set thì lấy của parent default nếu có
        if(!$objMeta->dataType)
            if($ret = parent::getHardCodeMetaObj($field))
                return $ret;

        return $objMeta;
    }

    public function _cccd($obj, $val, $field)
    {
        if($obj =  EventUserInfo::where("id", $obj->user_event_id)->first()){
            return $obj->id_number;
        }



    }
    public function _khau_tru($obj, $val, $field)
    {
        if($obj->payed)
            return 100 * number_format(($obj->khau_tru / $obj->payed) , 4)   .  " % ";

    }
    public function executeBeforeIndex($param = null)
    {

        $evStr = $this->getSNameFromField('event_id');
//        die($evStr);
        $evId = request("seby_$evStr");
        if($evId){

            //Tìm các user của sự kiện này
            $listUserEventId = EventAndUser::where('event_id', $evId)->pluck('user_event_id')->toArray();

            foreach ($listUserEventId as $ueid){
                $uev = EventUserInfo::find($ueid);
                //Xem trong EventUserPayment có user_event_id, event_id nào trong danh sách này ko
                if($obj = EventUserPayment::where('user_event_id', $ueid)
                    ->where('event_id', $evId)
                    ->first()){

                        if($uev){
                            $save = 0;
                            if($obj->bank_name != $uev->bank_name_text){

                                $obj->bank_name = $uev->bank_name_text;
                                $save = 1;
                            }
                            if($obj->bank_account != $uev->bank_acc_number){
                                $obj->bank_account = $uev->bank_acc_number;
                                $save = 1;
                            }
                            if($obj->tax_number != $uev->tax_number){
                                $obj->tax_number = $uev->tax_number;
                                $save = 1;
                            }

                            // ===== AUTO CALCULATE khau_tru IF NULL =====
                            if(is_null($obj->khau_tru) && !empty($obj->payed)){
                                $khauTruCalculated = 0;
                                if($uev->payment_type == 'trong_nuoc'){
                                    // 10% cho thanh toán trong nước
                                    $khauTruCalculated = $obj->payed * 0.10;
                                } else if($uev->payment_type == 'nuoc_ngoai'){
                                    // 20% cho thanh toán nước ngoài
                                    $khauTruCalculated = $obj->payed * 0.20;
                                }

                                if($khauTruCalculated != $obj->khau_tru){
                                    $obj->khau_tru = $khauTruCalculated;
                                    $save = 1;
                                }
                            }

                            if($save) {
                                $obj->addLog("Auto update bank info from EventUserInfo id=$ueid");
                                $obj->save();
                            }
                        }
                    }
                else{
                    //Chưa có bản ghi nào, Tạo mới các bản ghi trong EventUserPayment
                    $new = new EventUserPayment();
                    $new->event_id = $evId;
                    $new->user_event_id  = $ueid;


                    if($uev){

//                        die(" $uev->bank_name_text ");
                        $new->bank_name = $uev->bank_name_text;
                        $new->bank_account = $uev->bank_acc_number;
                        $new->tax_number = $uev->tax_number;
                    }
                    $new->save();
                }

            }
        }

    }


    function _image_list1($obj, $val, $field){
        return Helper1::imageShow1($obj, $val, $field);
    }

    function _bank_name($obj, $val, $field){
        $user_event_id = $obj->user_event_id;
        if(!$user_event_id)
            return "Not found user event id: $obj->user_event_id";
        $evu = EventUserInfo::find($user_event_id);
        if(!$evu)
            return "Not found user event id: $obj->user_event_id";
        $text = $evu->bank_name_text;
        return $text ? $text : "<div> &#9888; Chưa có thông tin<br> ngân hàng</div>";
    }
    function _bank_account($obj, $val, $field){
        $user_event_id = $obj->user_event_id;
        if(!$user_event_id)
            return "Not found user event id: $obj->user_event_id";
        $evu = EventUserInfo::find($user_event_id);
        if(!$evu)
            return "Not found user event id: $obj->user_event_id";
        $text = $evu->bank_acc_number;
        return $text ? $text : "<div> &#9888; Chưa có thông tin <br> ngân hàng</div>";
    }
    function _tax_number($obj, $val, $field){
        $user_event_id = $obj->user_event_id;
        if(!$user_event_id)
            return "Not found user event id: $obj->user_event_id";
        $evu = EventUserInfo::find($user_event_id);
        if(!$evu)
            return "Not found user event id: $obj->user_event_id";
        $text = $evu->tax_number;
        return $text ? $text : "<div> &#9888; Chưa có <br> mã số thuế</div>";
    }

    public function extraContentIndexButton1($v1 = null, $v2 = null, $v3 = null)
    {
        $sname = $this->getSNameFromField('event_id');
        $key = "seby_$sname";
        $evid = request($key);

        echo "<a href='/admin/event-user-info?_event_id_=$evid'><button class='float-right mt-2 ml-3 btn btn-sm btn-default' type='button'> Bảng User </button></a>";

        if($evid) {
            echo "<a title='Tải xuống Excel thanh toán của Sự kiện này' href='/tool1/_site/event_mng/payment/download_payment_event.php?evid=$evid'>
<button class='float-right mt-2 ml-3 btn btn-sm btn-primary' type='button'> Duyệt Thanh toán </button> </a> ";
        } else {
            echo "<button class='float-right mt-2 ml-3 btn btn-sm btn-info' type='button' onclick='checkEventSelected()'> Duyệt Thanh toán </button>";
        }
    }

    public function extraJsInclude()
    {
        ?>
        <script>
        function checkEventSelected() {
            let selectEvent = document.querySelector('select.select_event1');

            if (!selectEvent) {
                alert('Không tìm thấy select sự kiện');
                return false;
            }

            let value = selectEvent.value || selectEvent.options[selectEvent.selectedIndex]?.value;

            if (value === '' || value === null) {
                alert('⚠️ Bạn chưa chọn sự kiện');
                selectEvent.focus();
                return false;
            }

            // Nếu có giá trị, điều hướng đến URL
            let url = `/tool1/_site/event_mng/payment/download_payment_event.php?evid=${value}`;
            window.open(url, '_blank');
            return false;
        }


        // ===== GLOBAL VARIABLE LƯU TỶ LỆ KHAU_TRU/PAYED =====
        window.rateMoney = {};

        document.addEventListener('DOMContentLoaded', function() {
            // Hàm để tính thuc_nhan = payed - khau_tru cho một div.divTable2Row
            function calculateThucNhanForRow(divRow) {
                if (!divRow) return;

                let payedInput = divRow.querySelector('input.input_value_to_post.payed');
                let khauTruInput = divRow.querySelector('input.input_value_to_post.khau_tru');
                let thucNhanInput = divRow.querySelector('input.input_value_to_post.thuc_nhan');

                if (!payedInput || !khauTruInput || !thucNhanInput) {
                    return;
                }

                // Lấy giá trị payed và khau_tru
                let payed = parseFloat(payedInput.value) || 0;
                let khauTru = parseFloat(khauTruInput.value) || 0;

                // Kiểm tra khau_tru là số và không âm
                if (isNaN(khauTru) || khauTru < 0) {
                    thucNhanInput.value = '';
                    return;
                }

                // Tính thuc_nhan = payed - khau_tru
                let thucNhan = payed - khauTru;

                // Cập nhật giá trị thuc_nhan
                thucNhanInput.value = thucNhan >= 0 ? thucNhan : 0;
            }

            // Hàm để tính khau_tru dựa trên tỷ lệ khi payed thay đổi
            function calculateKhauTruFromRate(divRow) {
                if (!divRow) return;

                let dataId = divRow.getAttribute('data-id');
                if (!dataId) {
                    console.warn('divTable2Row không có data-id');
                    return;
                }

                let payedInput = divRow.querySelector('input.input_value_to_post.payed');
                let khauTruInput = divRow.querySelector('input.input_value_to_post.khau_tru');
                let thucNhanInput = divRow.querySelector('input.input_value_to_post.thuc_nhan');

                if (!payedInput || !khauTruInput || !thucNhanInput) {
                    return;
                }

                // Lấy giá trị payed mới
                let payed = parseFloat(payedInput.value) || 0;

                // Nếu có tỷ lệ đã lưu, tính khau_tru theo tỷ lệ đó
                if (window.rateMoney[dataId] !== undefined && window.rateMoney[dataId] > 0) {
                    let newKhauTru = payed * window.rateMoney[dataId];
                    // Làm tròn 4 chữ số thập phân
                    newKhauTru = Math.round(newKhauTru * 10000) / 10000;
                    khauTruInput.value = newKhauTru > 0 ? newKhauTru : 0;
                }

                // Tính lại thuc_nhan
                calculateThucNhanForRow(divRow);

                // Trigger change event
                thucNhanInput.dispatchEvent(new Event('change', { bubbles: true }));
            }

            // Hàm để attach listener cho input khau_tru trong một div.divTable2Row
            function attachKhauTruListener(khauTruInput, divRow) {
                if (!khauTruInput) return;

                khauTruInput.addEventListener('input', function() {
                    let divRow = this.closest('div.divTable2Row');
                    if (!divRow) {
                        console.warn('Không tìm thấy div.divTable2Row cha');
                        return;
                    }

                    // Tính thuc_nhan
                    calculateThucNhanForRow(divRow);

                    // Trigger change event để cập nhật lại UI nếu cần
                    let thucNhanInput = divRow.querySelector('input.input_value_to_post.thuc_nhan');
                    if (thucNhanInput) {
                        thucNhanInput.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });
            }

            // Hàm để attach listener cho input payed trong một div.divTable2Row
            function attachPayedListener(payedInput, divRow) {
                if (!payedInput) return;

                payedInput.addEventListener('input', function() {
                    let divRow = this.closest('div.divTable2Row');
                    if (!divRow) {
                        console.warn('Không tìm thấy div.divTable2Row cha');
                        return;
                    }

                    // Tính khau_tru theo tỷ lệ đã lưu
                    calculateKhauTruFromRate(divRow);
                });
            }

            // Hàm để setup một row
            function setupRow(divRow) {
                if (!divRow) return;

                let dataId = divRow.getAttribute('data-id');
                if (!dataId) return;

                let payedInput = divRow.querySelector('input.input_value_to_post.payed');
                let khauTruInput = divRow.querySelector('input.input_value_to_post.khau_tru');

                if (!payedInput || !khauTruInput) return;

                // ===== KHI LOAD TRANG, LƯU TỶ LỀ =====
                let payed = parseFloat(payedInput.value) || 0;
                let khauTru = parseFloat(khauTruInput.value) || 0;

                if (payed > 0 && khauTru >= 0) {
                    window.rateMoney[dataId] = khauTru / payed;
                    console.log(`📊 Saved rate for row ${dataId}: ${window.rateMoney[dataId]}`);
                }

                // Tính thuc_nhan lần đầu
                calculateThucNhanForRow(divRow);

                // Attach listeners
                attachKhauTruListener(khauTruInput, divRow);
                attachPayedListener(payedInput, divRow);
            }

            // ===== KHI TRANG LOAD, SETUP TẤT CẢ DIV.DITABLE2ROW =====
            document.querySelectorAll('div.divTable2Row').forEach(function(divRow) {
                setupRow(divRow);
            });

            // Nếu có mutation (thêm div.divTable2Row mới), setup row đó
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes.length) {
                        mutation.addedNodes.forEach(function(node) {
                            if (node.nodeType === 1) { // Element node
                                // Kiểm tra nếu node là div.divTable2Row
                                if (node.classList && node.classList.contains('divTable2Row')) {
                                    setupRow(node);
                                }
                                // Hoặc tìm div.divTable2Row trong node con
                                let divRows = node.querySelectorAll?.('div.divTable2Row');
                                if (divRows && divRows.length > 0) {
                                    divRows.forEach(setupRow);
                                }
                            }
                        });
                    }
                });
            });

            // Bắt đầu observe document body
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        });

        </script>
        <?php
    }

    function _payment_type($obj, $val, $field)
    {
        if($obj->payment_type == 'trong_nuoc')
            return "<span style='color: green'> Trong nước </span>";
        if($obj->payment_type == 'nuoc_ngoai')
            return "<span style='color: royalblue'>  Nước ngoài </span>";
        return "<span style='color: red'>  ??? </span>";
    }

    function getSqlOrJoinExtraIndex(\Illuminate\Database\Eloquent\Builder &$x = null, $getSelect = 0)
    {
        if(Helper1::isMemberModule()){
            $mEventId = EventInfo::getEventIdListInDeparmentOfUser(getCurrentUserId());
            $x->whereIn('event_id',  $mEventId);
        }

        return $x
            ->leftJoin('event_user_infos', 'user_event_id', '=', 'event_user_infos.id')
            ->addSelect([
                'event_user_infos.email AS _email',
                'event_user_infos.first_name as _first_name',
                'event_user_infos.last_name as _last_name',
                'event_user_infos.payment_type as payment_type',
            ]);
    }

    function getMapJoinFieldAlias()
    {
        return [
            '_email'=>'event_user_infos.email',
            '_first_name'=>'event_user_infos.first_name',
            '_last_name'=>'event_user_infos.last_name',
        ];
    }
    public function getFullSearchJoinField()
    {
        return [
            'event_user_infos.first_name'  => "like",
            'event_user_infos.last_name'  => "like",
            'event_user_infos.organization'  => "like",
            'event_user_infos.email'   => "like",
        ];
    }
    //...

    public function _user_event_id($obj, $valIntOrStringInt, $field)
    {
        $objU = EventUserInfo::find($valIntOrStringInt);
        if(!$objU)
            return "Not found user : $valIntOrStringInt";
        $img = "/images/code_gen/ncbd-event-$obj->event_id-".$objU->id.".png";

        if(!file_exists(public_path($img))){


//            echo "\n Not found IMG";
        }

        $_group_name = $obj->_group_name;

        $domain = UrlHelper1::getDomainHostName();
        $img = EventInfoControllerApi::genLinkQr($domain, $obj->event_id, $objU->email, $objU->id);

        $org = $objU->organization ? "<br>  $objU->organization" : '';
        $designation = $objU->designation ? " <br>  $objU->designation" : '';
        $_group_name = $_group_name ? "<br> Nhóm: $_group_name" : '';

        $uid1 = $objU->id;

        $module = Helper1::getModuleCurrentName();

        $ret = "<div data-code-pos='ppp17121128641' style='font-size: small; padding: 5px; color: royalblue; position: relative'>";
        $ret .= " <span class='uinfo_print' id='user_info_$uid1'>
  <a style='text-decoration: none' href='/$module/event-user-info/edit/$uid1' target='_blank'>
  <i class='fa fa-edit'></i>
  $objU->title $objU->last_name $objU->first_name
 </a>
 $designation
 $org
 $_group_name
";
        $ret .= '</span>';

        //document.cookie = "isShowQrCode
        //Nếu cookie này cho phép thì mới hiện ảnh:
        $display = ";display: none;";
        if( ($_COOKIE['isShowQrCode'] ?? '')  && $_COOKIE['isShowQrCode'] != 'false') {
            $display = ";display: block;";
//            echo(" isShowQrCode = " . $_COOKIE['isShowQrCode']);
        }

        $module = Helper1::getModuleCurrentName();


        $ret .= ' <DIV class="img_qr_code" style="height: 101px; '.$display.'"><img style="width: 100px" src="'.$img.'"></DIV>';
        $ret .= '</div>';

        return $ret;
    }

    public function extraContentIndex1($v1 = null, $v2 = null, $v3 = null)
    {

        $uid = getCurrentUserId();
        if(Helper1::isMemberModule()){
//            $mmEv = EventInfo::where('user_id', $uid)->latest()->get();
            $mmEv = EventInfo::getEventIdListInDeparmentOfUser($uid, 1);

        }
        else
            $mmEv = EventInfo::latest()->get();

        $linkOpt = UrlHelper1::getUriWithoutParam();
        $sname = $this->getSNameFromField('event_id');
        $key = "seby_$sname";

        EventInfo::getHtmlSelectEvent($linkOpt, $mmEv, $key);

        ?>

        <!--        <button title="Chọn các thành viên dưới đây để in mã QR" class="btn btn-sm btn-primary mb-3" id="print_qr_list"> In mã QR</button>-->



        <?php
    }

    public function _payed($obj, $valIntOrStringInt, $field) {

        if($obj->payment_type == 'nuoc_ngoai'){
            return $valIntOrStringInt . " USD ";
        }

        $moneyVn = cstring2::toTienVietNamString3($valIntOrStringInt);
        return $moneyVn;
    }

    public function _thuc_nhan($obj, $valIntOrStringInt, $field) {

        if($obj->payment_type == 'nuoc_ngoai'){
            return $valIntOrStringInt . " USD ";
        }
        $moneyVn = cstring2::toTienVietNamString3($valIntOrStringInt);
        return $moneyVn . " VND ";
    }

    public function _event_id($obj, $valIntOrStringInt, $field) {

        $key = EventAndUser_Meta::getSearchKeyFromField('event_id');

        if(!request($key))
            if($objU = EventInfo::find($valIntOrStringInt)){
                $ret = "<div title='$objU->name' data-code-pos='ppp 1'style='font-size: small; padding: 5px; color: royalblue'>";
                $ret .= "" . cstring2::substr_fit_char_unicode($objU->name,0, 50,1);
                $ret .= '</div>';
                return $ret;
            }
//        return $valIntOrStringInt;
    }



    public function extraCssInclude()
    {
?>
        <style>

            .fa.fa-file-excel{
                display: none;
            }

            .join_val div {
                padding: 10px;
                font-size: 80%;
            }
        </style>
    <?php
    $key = EventAndUser_Meta::getSearchKeyFromField('event_id');
    if(request($key)){
        ?>
        <style>


    div.cellHeader.event_id{
        display: none;
    }
    div[data-table-field=event_id]{
        display: none!important;
    }
</style>

    <?php
    }


    }
}
