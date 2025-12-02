<?php

namespace App\Models;

use App\Components\Helper1;
use LadLib\Common\Database\MetaOfTableInDb;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Meta;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class OrderInfo_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/order-info';

    protected static $web_url_admin = '/admin/order-info';

    protected static $api_url_member = '/api/member-order-info';

    protected static $web_url_member = '/member/order-info';

    //public static $folderParentClass = OrderInfoFolderTbl::class;
    public static $modelClass = OrderInfo::class;

    public static $titleMeta = "Lịch sử đơn mua";

//    public static $enableAddMultiItem = 1;

    public static function enableAddMultiItem()
    {
        if(Helper1::isAdminModule())
            return 1;
        return 0;
    }

    function _name($obj1, $val, $field)
    {

        if(!$obj1)
            return;
        $obj = OrderInfo::find($obj1->id);
        // Return service description from infos JSON
        if (!$obj || !isset($obj->infos)) {
            return "Đơn hàng";
        }

        try {
            $infos = json_decode($obj->infos, true);

            if (!is_array($infos)) {
                return "Đơn hàng";
            }

            // Build VPS service description
            $post = $infos['post'] ?? null;

            if ($post === 'vps') {
                // Get configuration
                $config = $infos['configuration'] ?? [];
                $breakdown = $infos['breakdown'] ?? [];
                $totalPrice = $infos['total_price_formatted'] ?? '0đ';

                // Build HTML table with 4 columns - Mục | Giá | Số lượng | Thành tiền
                $html = '<table style="font-size: 0.9rem; width: 100%; border-collapse: collapse; margin-top: 10px;">';
                $html .= '<thead style="background: #f8f9fa;">';
                $html .= '<tr>';
                $html .= '<th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">Mục</th>';
                $html .= '<th style="border: 1px solid #dee2e6; padding: 8px; text-align: right;">Giá</th>';
                $html .= '<th style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">Số lượng</th>';
                $html .= '<th style="border: 1px solid #dee2e6; padding: 8px; text-align: right;">Thành tiền</th>';
                $html .= '</tr>';
                $html .= '</thead>';
                $html .= '<tbody>';

                // CPU
                if (isset($breakdown['cpu'])) {
                    $cpu = $breakdown['cpu'];
                    $pricePerUnit = $cpu['total'] > 0 && $cpu['quantity'] > 0 ? number_format($cpu['total'] / $cpu['quantity'], 0, ',', '.') : '0';
                    $html .= '<tr>';
                    $html .= '<td style="border: 1px solid #dee2e6; padding: 8px;">CPU</td>';
                    $html .= '<td style="border: 1px solid #dee2e6; padding: 8px; text-align: right;">' . $pricePerUnit . 'đ</td>';
                    $html .= '<td style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">' . $cpu['quantity'] . '</td>';
                    $html .= '<td style="border: 1px solid #dee2e6; padding: 8px; text-align: right; font-weight: bold;">' . number_format($cpu['total'], 0, ',', '.') . 'đ</td>';
                    $html .= '</tr>';
                }

                // RAM
                if (isset($breakdown['ram'])) {
                    $ram = $breakdown['ram'];
                    $pricePerUnit = $ram['total'] > 0 && $ram['quantity'] > 0 ? number_format($ram['total'] / $ram['quantity'], 0, ',', '.') : '0';
                    $html .= '<tr>';
                    $html .= '<td style="border: 1px solid #dee2e6; padding: 8px;">RAM</td>';
                    $html .= '<td style="border: 1px solid #dee2e6; padding: 8px; text-align: right;">' . $pricePerUnit . 'đ</td>';
                    $html .= '<td style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">' . $ram['quantity'] . '</td>';
                    $html .= '<td style="border: 1px solid #dee2e6; padding: 8px; text-align: right; font-weight: bold;">' . number_format($ram['total'], 0, ',', '.') . 'đ</td>';
                    $html .= '</tr>';
                }

                // Disk
                if (isset($breakdown['disk'])) {
                    $disk = $breakdown['disk'];
                    $pricePerUnit = $disk['total'] > 0 && $disk['quantity'] > 0 ? number_format($disk['total'] / $disk['quantity'], 0, ',', '.') : '0';
                    $html .= '<tr>';
                    $html .= '<td style="border: 1px solid #dee2e6; padding: 8px;">Storage</td>';
                    $html .= '<td style="border: 1px solid #dee2e6; padding: 8px; text-align: right;">' . $pricePerUnit . 'đ</td>';
                    $html .= '<td style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">' . $disk['quantity'] . '</td>';
                    $html .= '<td style="border: 1px solid #dee2e6; padding: 8px; text-align: right; font-weight: bold;">' . number_format($disk['total'], 0, ',', '.') . 'đ</td>';
                    $html .= '</tr>';
                }

                // Network (Dedicated or Shared)
                if (isset($breakdown['network_dedicated']) && $breakdown['network_dedicated']['total'] > 0) {
                    $net = $breakdown['network_dedicated'];
                    $html .= '<tr>';
                    $html .= '<td style="border: 1px solid #dee2e6; padding: 8px;">Network</td>';
                    $html .= '<td style="border: 1px solid #dee2e6; padding: 8px; text-align: right;">1.000đ/100Mbps</td>';
                    $html .= '<td style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">' . ($net['bandwidth'] ?? '0') . ' Mbps</td>';
                    $html .= '<td style="border: 1px solid #dee2e6; padding: 8px; text-align: right; font-weight: bold;">' . number_format($net['total'], 0, ',', '.') . 'đ</td>';
                    $html .= '</tr>';
                }

                // IP Address
                if (isset($breakdown['ip_address']) && $breakdown['ip_address']['total'] > 0) {
                    $ip = $breakdown['ip_address'];
                    $html .= '<tr>';
                    $html .= '<td style="border: 1px solid #dee2e6; padding: 8px;">IP Address</td>';
                    $html .= '<td style="border: 1px solid #dee2e6; padding: 8px; text-align: right;">50đ</td>';
                    $html .= '<td style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">' . ($ip['detail'] ?? '0') . '</td>';
                    $html .= '<td style="border: 1px solid #dee2e6; padding: 8px; text-align: right; font-weight: bold;">' . number_format($ip['total'], 0, ',', '.') . 'đ</td>';
                    $html .= '</tr>';
                }

                // Total row
                $html .= '<tr style="background: #e8f4f8; font-weight: bold;">';
                $html .= '<td colspan="3" style="border: 1px solid #dee2e6; padding: 10px;">Tổng cộng</td>';
                $html .= '<td style="border: 1px solid #dee2e6; padding: 10px; text-align: right; color: #dc3545; font-size: 1.1rem;">' . $totalPrice . '</td>';
                $html .= '</tr>';

                $html .= '</tbody>';
                $html .= '</table>';

                return $html;
            }

            // Fallback for other service types
            return "Đơn hàng #" . ($obj->id ?? '');

        } catch (\Exception $e) {
            return "Đơn hàng";
        }
    }

    public function isUseRandId()
    {
        if(SiteMng::use_snowflake_models("OrderInfo"))
            return 0;

        return 1;
    }

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();

        if ($field == 'note1' || $field == 'note2' || $field == 'log') {
            $objMeta->dataType = DEF_DATA_TYPE_TEXT_AREA;
        }

        if ($field == 'status') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }

        if ($field == 'order_status') {
            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;
        }

        if ($field == 'image_list') {
            $objMeta->dataType = DEF_DATA_TYPE_IS_MULTI_IMAGE_BROWSE;
            //            $objMeta->join_func = 'App\Models\News_Meta::joinFuncImageId';
        }

        if ($field == 'phone_request') {
            //            $objMeta->join_api_field = 'name';
            //            $objMeta->join_relation_func = 'joinTags';
            //            $objMeta->join_api = '/api/tags/search';
            $objMeta->join_api_field = 'phone_number';
            //            $objMeta->join_relation_func = 'joinUsers';
            $objMeta->join_api = '/api/transport-info/search';
            $objMeta->dataType = DEF_DATA_TYPE_NUMBER;
            $objMeta->opt_field = 3;
        }

        if ($field == 'service_require') {
            //            $objMeta->dataType = DEF_DATA_TYPE_TEXT_STRING;
            //            $objMeta->opt_field = 3;
        }

        if (! $objMeta->dataType) {
//            return null;
        }
        if ($field == 'user_id') {
            $objMeta->join_api_field = 'email';
            //            $objMeta->join_func = 'joinUserEmailUserId';
            $objMeta->join_api = '/api/user/search';
        }
        return $objMeta;
    }

    public function _to_address($obj, $val, $field)
    {

        ?>
        <?php
    }

    public function _user_id($obj, $val)
    {
        return User_Meta::search_user_email($obj, $val);
    }

    public function extraCssIncludeEdit()
    {
        ?>

        <style>
            .quantity_input {
                text-align: center;
            }
            .minus_one , .plus_one {
                border: 1px solid #ccc;
                padding: 5px 10px;
                background-color: white;
                text-align: center;
                cursor: pointer;
                font-weight: bold;
                margin: 3px;
            }
        </style>
        <?php
    }

    public function extraCssInclude()
    {
        ?>

        <style>
            input[data-field=service_require]{
                display: none;
            }
        </style>

        <?php
    }

    public function _cmd($obj)
    {

        $tmp = '';
        $send = 0;
        if ($ods = OrderShip::where('order_id', $obj->id)->first()) {

            $tmp = "<br>(Đã gửi, Mã tra: <a target='_blank' href='/admin/order-ship/edit/$ods->id'> $ods->remote_tracking_id </a>)";
            $send = 1;
        }

        return "<div> <button class='send_don_to_ship_api' data-sent-ship-done='$send' data-id-order='$obj->id' type='button'> Gửi_Đơn </button> $tmp </div>";
    }

    public function _order_status($objData = null, $value = null, $field = null)
    {
        $mm = [
            0 => '- Chưa xử lý',
            DEF_SHOP_STATUS_TELE_KHACH_CHOT_DON => '- Khách chốt đơn',
            DEF_SHOP_STATUS_TELE_DON_HUY_BOI_SHOP => 'Shop Hủy Đơn',
            DEF_SHOP_STATUS_TELE_KHACH_HUY_DON => 'Khách Hủy đơn',
            DEF_SHOP_STATUS_TELE_HOAN_TRA_LAI_HANG => 'Hoàn trả',
            DEF_SHOP_STATUS_TELE_THONG_TIN_KHACH_SAI => 'Thông tin sai',
            DEF_SHOP_STATUS_TELE_SAI_SO_PHONE => 'Sai số điện thoại',
            DEF_SHOP_STATUS_TELE_KHONG_LIEN_LAC_DUOC => 'Không liên lạc được',
            -1 => 'Trạng thái',
            DEF_SHOP_STATUS_TELE_DA_THONG_TIN_DON_SANG_SHIP => '- Đơn đã báo Ship',
            DEF_SHOP_STATUS_TELE_SHIP_DONE_TO_KHACH_HANG => '- Ship Done',
        ];

        //Nếu có obj có thì mới trả lại Key=>id
        //Nếu ko, nghĩa là trường hợp Get all để chọn
        if ($objData) {
            if (isset($mm[$value]) && $value) {
                return [$value => $mm[$value]];
            } else {
                return null;
            }
        }

        return $mm;
    }

    public function extraJsIncludeEdit($objData = null)
    {
        ?>

        <script>



        </script>

        <?php


        self::extraJsInclude(); // TODO: Change the autogenerated stub
    }

    public function extraJsInclude()
    {

        if (!Helper1::isAdminModule(request())) {
//            return;
        }

        ?>
        <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
        <script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs@gh-pages/qrcode.min.js"></script>

        <style>
            select[data-field=order_status] {
                min-width: 130px;
            }
            div.auto_address span{
                display: none;
                color: red;
            }
            div.auto_address {
                min-width: 150px;
            }
            div.auto_address span:hover{
                cursor: pointer;
            }
            div.auto_address input{
                border: 1px solid #ccc!important;
                width: 88% !important;
            }
            input[data-field=to_address] {
                display: none;
            }
        </style>


        <script>


            function printDiv(divName){
                var printContents = document.getElementById(divName).innerHTML;
                var originalContents = document.body.innerHTML;
                document.body.innerHTML = printContents;
                window.print();
                document.body.innerHTML = originalContents;
            }

            function CallPrint(htmlx) {

                let WinPrint = window.open('', '', 'left=0,top=0,width=800,height=800,toolbar=1,scrollbars=1,status=0');
                WinPrint.document.write('<html><head><title>In Đơn</title></head>');
                // WinPrint.document.write('<style>@page {size: A6 landscape;margin: 1%;}</style>');
                // WinPrint.document.write('<body style="font-family:verdana; font-size:14px;width:370px;height:270px:" >');
                WinPrint.document.write('<script src="/adminlte/plugins/jquery/jquery.min.js"></\script>');
                WinPrint.document.write('<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></\script>');
                WinPrint.document.write('<script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs@gh-pages/qrcode.min.js"></\script>');
                WinPrint.document.write('<link rel="stylesheet" href="/assert/css/lad-common.css">');
                WinPrint.document.write('<link rel="stylesheet" href="/assert/css/print_ship.css">');
                WinPrint.document.write('<style type="text/css">  </style>');
                WinPrint.document.write('<body style="" >');
                WinPrint.document.write(htmlx);
                WinPrint.document.write('<script src="/template/shop1/js/print_orders.js"></\script>');
                WinPrint.document.write('</body></html>');
                // WinPrint.document.close();
                WinPrint.focus();

                // WinPrint.close();
                // prtContent.innerHTML = "";
            }

            $("#print_orders").on('click', function (){
                let user_token = jctool.getCookie('_tglx863516839');
                console.log("Print ...");

                let mPrint = [];
                $(".divTable2Row input.select_one_check:checked").each(function () {
                    if (this.checked && $(this).attr("data-id")) {
                        let nodeId = $(this).attr("data-id");
                        console.log("Print now: " + nodeId);
                        // idListSelecting +="," + $(this).attr("data-id")
                        mPrint.push(nodeId);

                        $("i[data-field=print_status][data-id="+ nodeId + "]").removeClass('fa-toggle-off');
                        $("i[data-field=print_status][data-id="+ nodeId + "]").addClass('fa-toggle-on');
                        $("input.input_value_to_post.print_status[data-id="+ nodeId + "]").val(1);

                    }
                });

                if(mPrint.length <=0){
                    alert("Bạn chưa chọn đơn in?");
                    return;
                }



                let url = "/api/order-info/getHtmlPrintOrder";
                $.ajax({
                    url: url,
                    type: 'POST',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                    },
                    data: {datax: mPrint},
                    success: function (data, status) {
                        console.log("Data ret: ", data, " \nStatus: ", status);
                        if(data.message && data.message == 'html_ready')
                        if(data.payload){
                            CallPrint(data.payload);
                            console.log(" trigger save all Data ");
                            $("#save-all-data").click();
                        }
                    },
                    error: function (data) {
                        console.log(" DATAx " , data);
                        if(data.responseJSON && data.responseJSON.message)
                            alert('Error call api: ' + "\n" + data.responseJSON.message)
                        else
                            alert('Error call api: ' + "\n" + url + "\n\n" + JSON.stringify(data).substr(0,1000));
                    }
                });


                //printDiv("all_van_don_ship")
                // let prtContent = document.getElementById('all_van_don_ship');
                // CallPrint(prtContent.innerHTML);
                //jsRetPrint
            })

            $(function (){
                // JsBarcode(".bar_code").init();
                // console.log("JsBarcode init ");

            })

        </script>

        <script>

            $(".send_don_to_ship_api").on("click", function (){

                let token = jctool.getCookie('_tglx863516839');

                let dataId = $(this).attr("data-id-order");

                console.log("Gửi đơn này tới đối tác vận chuyển..., mã đơn: " + dataId)

                // $(".divTable2Cell i.save_one_item[data-id="+ dataId +"]").click();


                var allData = {};
                let isEmpty = 1;
                $("input.input_value_to_post[data-id='" + dataId + "'], textarea.input_value_to_post[data-id='" + dataId + "']").each(function (){
                    if($(this).hasClass('input_value_to_post')){
                        // console.log(" FIELD = " + $(this).data('field') + " / val = " +  $(this).val());
                        if($(this).attr('data-edit-able') == 1){
                            let value = $(this).val();
                            // console.log(" Editable data-edit-able: ", $(this).data('data-edit-able'));
                            console.log("Editable field: ", $(this).data('field'), value, $(this).attr('value'));
                            if(value || value === 0 || value === '0')
                                isEmpty = 0;
                            allData[$(this).data('field')] = value;
                        }
                    }
                })

                console.log("allData2: ", allData);

                //
                if(isEmpty && dataId < 0){
                    console.log("Không update vì ko có giá trị insert nào!")
                }


                // setTimeout(function () {


                    let url = "/api/order-info/send_to_ghtk?dataId=" + dataId;
                    $.ajax({
                        url: url,
                        type: 'POST',
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('Authorization', 'Bearer ' + token);
                        },
                        data: allData,
                        success: function (data, status) {
                            console.log("Data: ", data, " \nStatus: ", status);
                            if(!data.payload){
                                alert("Có lỗi xảy ra, cấu trúc trả lại không hợp lệ, payload!")
                                return;
                            }

                            if(!data.message)
                                data.message = '';
                            if(!data.payload)
                                data.payload = '';

                            if(data.code > 0){
                                showToastInfoTop("Đơn đã gửi Thành công!<br>" + data.message + '<br>' + data.payload)
                                setDoneColorOrder(dataId)
                            }
                            else{

                                alert("Thông báo: " + data.message + data.payload)
                            }

                        },
                        error: function (data) {
                            console.log(" Data ret " , data);
                            data = data.responseJSON;
                            if(data.payload) {
                                if (!data.message)
                                    data.message = '';
                                if (!data.payload || data.payload == data.message)
                                    data.payload = '';
                                alert("Có lỗi gửi đơn:\n" + data.message + data.payload)
                                return;
                            }

                            alert("Lỗi không xác định!");
                        },
                    });
                // }, 1000);

            })

            $(".divTable2Cell select.sl_option[data-field=order_status]").on('change', function () {
                let idf = $(this).attr("data-id")
                console.log("Change select ...", idf);
                setTimeout(function () {
                    $(".divTable2Cell i.save_one_item[data-id="+ idf +"]").click();
                }, 100);
            })


        </script>



        <script data-code-pos="ppp1679218479092">

            function setJsForNewCont(){

                console.log("  Load extraJsIncludeEdit");
                document.querySelectorAll('.minus_one').forEach(function(item) {
                    console.log("......1111");
                    item.addEventListener('click', function() {
                        console.log("  Click minus_one");
                        const input = this.nextElementSibling;
                        if (input && input.classList.contains('quantity_input')) {
                            let value = parseInt(input.value, 10);
                            if (!isNaN(value) && value > 0) {
                                input.value = value - 1;
                            }
                        }
                    });
                });

                document.querySelectorAll('.plus_one').forEach(function(item) {
                    item.addEventListener('click', function() {
                        console.log("  Click plus_one");
                        const input = this.previousElementSibling;
                        if (input && input.classList.contains('quantity_input')) {
                            let value = parseInt(input.value, 10);
                            if (!isNaN(value)) {
                                input.value = value + 1;
                            }
                        }
                    });
                });

            }

            $( function() {

                let globalBillId
                let client_session_time
                let globalDataClass

                let user_token = jctool.getCookie('_tglx863516839');


                $("#confirm_product_list").click(function (){
                    console.log("Click confirm_product_list...");

                    let mmBill = [];
                    $('table.get_all_sku input[type=checkbox]:checked').each(function (){

                        let this1 = $(this);
                        let prtd = $(this).parent()
                        let sku = $(this).attr('data-sku-id');
                        let productId = $(this).attr('data-product-id');
                        console.log(" SKU = ", sku , 'proid = ', productId);

                        let quantity_input = prtd.siblings().find("input.quantity_input").val()
                        let price_input = prtd.siblings().find("input.price_input").val()
                        console.log(" quantity_input price_input = ", quantity_input , price_input);

                        let data = {
                            sku: sku,
                            productId: productId,
                            quantity_input: quantity_input,
                            price_input: price_input
                        }

                        mmBill.push(data);
                    })

                    if(mmBill.length <=0 )
                    {
                        if (!confirm("Bạn chưa chọn hàng hóa, số lượng?\nViệc này sẽ làm đơn hàng trống (Bỏ hàng hóa ra khỏi đơn)!\nBấm OK để xác nhận!"))
                            return;
                    }

                    let dataClass = '';

                    let url = "/api/member-order-info/postBill?order_id=" + globalBillId + '&client_session_time='
                        + client_session_time + "&globalDataClass="+globalDataClass;
                    $.ajax({
                        url: url,
                        type: 'POST',
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                        },
                        data: {bill_info: mmBill},
                        success: function (data, status) {
                            console.log("Data ret: ", data, " \nStatus: ", status);
                            if(data.payload !== undefined && data.payload.html_ret !== undefined){
                                $(".service_require_list_html[data-id="+ globalBillId +"]").html(data.payload.html_ret);
                                $("input[data-field=money][data-id=" + globalBillId +"]").val(data.payloadEx)

                                clsTableMngJs.updateListIdInsert(data.payload.new_bill);

                                $( "#dialog_select_product" ).dialog( "close");

                            }
                            // $("#product_list_all").html(data);
                            // $("#product_list_all").css({height:"100%", overflow:"auto"});
                        },
                        error: function (data) {
                            console.log(" DATAx " , data);
                            if(data.responseJSON && data.responseJSON.message)
                                alert('Error call api: ' + "\n" + data.responseJSON.message)
                            else
                                alert('Error call api: ' + "\n" + url + "\n\n" + JSON.stringify(data).substr(0,1000));
                        }
                    });
                })

                $(".delete_select_service").click(function (){
                    let dataId = $(this).attr('data-id');
                    globalBillId = dataId
                    client_session_time = Date.now();
                    globalDataClass = $(this).attr('data-class');
                    $("#confirm_product_list").trigger("click");
                })


                $(".btn_select_service").click(function (){

                    console.log(" Click select btn_select_service");



                    let dataId = $(this).attr('data-id');
                    globalBillId = dataId
                    client_session_time = Date.now();
                    globalDataClass = $(this).attr('data-class');

                    console.log("Click " , $(this).attr('data-id'));
                    $( "#dialog_select_product" ).dialog( "open" );

                    let url = "/api/member-order-info/getListProductSelect?billId=" + dataId;
                    $.ajax({
                        url: url,
                        type: 'GET',
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                        },
                        data: {},
                        success: function (data, status) {
                            console.log("Datax html: ", " \nStatus: ", status);
                            $("#product_list_all").html(data);
                            $("#product_list_all").css({height:"100%", overflow:"auto"});


                            setJsForNewCont();



                        },
                        error: function (data) {
                            console.log(" DATAx " , data);
                            if(data.responseJSON && data.responseJSON.message)
                                alert('Error call api: ' + "\n" + data.responseJSON.message)
                            else
                                alert('Error call api: ' + "\n" + url + "\n\n" + JSON.stringify(data).substr(0,1000));
                        }
                    });

                })
                $( "#dialog_select_product" ).dialog({
                    autoOpen: false,
                    height: 600,
                    width: 800,
                    modal: true,
                });
            } );
        </script>

        <script>

            function setDoneColorOrder(fid){

                $(".divTable2Row[data-id=" + fid + "] input[data-field!='id']:not([type=checkbox])").prop("disabled", true);
                $(".divTable2Row[data-id=" + fid + "] input[data-field!='id']:not([type=checkbox])").attr("disabled", true);
                $(".divTable2Row[data-id=" + fid + "] input[data-field!='id']:not([type=checkbox])").css("color", 'blue');

                $(".divTable2Row[data-id=" + fid + "][data-field!='id'] select ").prop("disabled", true);
                $(".divTable2Row[data-id=" + fid + "][data-field!='id'] select ").attr("disabled", true);
                $(".divTable2Row[data-id=" + fid + "][data-field!='id'] select ").css("color", 'blue');


                $(".divTable2Row[data-id=" + fid + "] .delete_select_service").hide()
                $(".divTable2Row[data-id=" + fid + "] .btn_select_service").hide()
                $(".divTable2Row[data-id=" + fid + "] .remove_this_itemtree").hide()
                // $(".divTable2Row[data-id=" + fid + "] .span_auto_complete1").removeClass('span_auto_complete1')
                $(".divTable2Row[data-id=" + fid + "] .span_auto_complete1").parent().text($(".divTable2Row[data-id=" + fid + "] .span_auto_complete1").text().replace("[x]", ''))

            }

            <?php

                //$uid = getUserIdCurrentInCookie();
                if (! Helper1::isAdminModule(request())) {
                    ?>

            setTimeout(function () {

                $('input.input_value_to_post[data-field=order_status]').each(function (){
                    console.log(" order_status val = ", $(this).val());
                    if($(this).val() > 1) {
                        let fid = $(this).attr('data-id')
                        setDoneColorOrder(fid);
                    }
                })

                // $(".send_don_to_ship_api").each(function (){
                //     //data-id-order
                //     //data-sent-ship-done
                //     if($(this).attr('data-sent-ship-done') == 1){
                //         console.log("Set done telesele: " + $(this).attr('data-id-order'));
                //         let fid = $(this).attr('data-id-order')
                //         setDoneColorOrder(fid);
                //     }
                // })
            }, 300);
            <?php
                }


        ?>

        </script>

        <style>
            .service_require_list_html{
                min-width: 200px;
            }
        </style>

        <div data-code-pos="ppp1679218472216" id="dialog_select_product" title="Chọn các Sản phẩm cho Đơn hàng này" style="margin: 0px">
            <div style="overflow:auto; padding: 10px 20px; background-color: snow; border-bottom: 1px solid #ccc">
                <input class="form-control"  style="width: 200px; display: inline-block" type="text" placeholder="Tìm tên sản phẩm">
                <div style="float: right; position: relative">

                    <button type="button" style="float: right; display: block" class="btn btn-info" id="confirm_product_list">
                        Xác nhận Đơn hàng</button>

                    <br>
                    <i style="font-size: small; float: right; display: block">(Chọn lại sản phẩm cho đơn - Chọn sản phẩm bên dưới và bấm Xác nhận)</i>
                </div>
            </div>
            <div id="product_list_all" style="overflow:auto; padding: 20px 20px 100px 20px">
            </div>
        </div>

        <?php
    }

    public function _service_require($obj, $val = null, $field = null)
    {

        if(Helper1::getCurrentActionMethod() != 'edit'){
            return;
        }


        $textToShip = '';
        $mBillP = OrderItem::where('order_id', $obj->id)->get();


        $ret = '';
        $totalPr = 0;
        if (count($mBillP)) {
            foreach ($mBillP as $billP) {
                $prod = Product::find($billP->product_id);
                $pr = $billP->price * $billP->quantity;
                $totalPr += $pr;
                $name = @$prod->name;
                $nameAndSKU = trim(trim("$name, $billP->sku_string"), ',');
                if($nameAndSKU)
                    $nameAndSKU .= ': ';

                $ret .= " <div style='margin-bottom: 2px; display: block; border: 1px solid #ccc; padding: 5px; background-color: lavender'> $nameAndSKU  SL: $billP->quantity x $billP->price = $pr VND </div> ";
                $textToShip .= "$billP->quantity x $name ($billP->sku_string); \n";
            }
        }

        if(count($mBillP) > 1)
            $ret .= " <b style='color: green'> Tổng giá: <span id='total_price_order'>$totalPr</span> VND </b>";

        if ($val == 'get_text_to_ship') {
            return $textToShip;
        }

        if ($val == 'get_only_ret_html') {
            return $ret;
        }

        $cls = static::$modelClass;



        $ret1 =  "<div class='service_require_list_html' data-id='$obj->id' data-code-pos='ppp1679274952879' style='font-size: small; padding: 5px'> $ret</div>";
        $ret2 = '';


        if(Helper1::isAdminModule() || \App\Models\Role::checkRouteNameAllowRoleId(DEF_GID_ROLE_MEMBER, "api.member-order-info.getListProductSelect"))
        $ret2 = "<button data-id='$obj->id' data-class='$cls'  class='btn_select_service' type='button' style='font-size: small; margin: 5px'>
        Chọn sản phẩm
        </button>
        <button data-id='$obj->id' data-class='$cls'  class='delete_select_service' type='button' style='font-size: small; margin: 5px'>
        Hủy đơn
        </button>
        ";

        if(Helper1::getCurrentActionMethod() == 'create'){

        }
//        if(Helper1::isAdminModule())
        else
            return $ret1 . $ret2;



        return $ret1;
    }

    public function _image_list($obj, $val, $field)
    {
        return Helper1::imageShow1($obj, $val, $field);
    }

    public function _phone_request($objData, $value = null, $field = null)
    {

        if (! $objData) {
            return null;
        }
        //        return "xxx1 $value / $field";
        //
        $phoneOK = $value;
        if ($phoneOK) {
            if ($phoneOK[0] != '0') {
                $phoneOK = "0$phoneOK";
            }
        }

        $zalo = "<a href='https://zalo.me/$phoneOK' target='_blank'> <img style='width: 30px' src='/images/icon/icon-zalo.png' alt=''></a>";
        $ret = '';
        if ($value && $obj = \App\Models\User::where('phone_number', $value)->first()) {
            $ret = "$zalo
 <span data-code-pos='ppp16654984' class='span_auto_complete1' title='Xem thành viên này'> <a href='/admin/user-api/edit/$obj->id'> <i class='fa fa-edit'></i> </a> </span>
 <span data-code-pos='ppp16654584' data-autocomplete-id='$objData->id-$field' class='span_auto_complete'
data-item-value='$obj->phone_number' title='Remove this item'>$obj->phone_number / $obj->email [x]</span>


";
            $obj = json_decode($obj);
            //return 'abc';
            if (Helper1::isApiCurrentRequest()) {
                return [$obj->phone_number => $obj->phone_number];
            }

            return $ret;
        } elseif ($value) {
            return "$zalo
 <span data-code-pos='ppp166584' class='span_auto_complete1' title='Thêm thành viên với số mới này'> <i class='fa fa-edit'></i> </span>
 <span title='Remove this item' data-code-pos='ppp1665' data-autocomplete-id='$objData->id-$field' class='span_auto_complete span_auto_complete1'>New Phone: $value [x] </span>


";
        }

        return null;
    }
}
