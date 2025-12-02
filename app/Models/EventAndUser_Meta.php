<?php

namespace App\Models;

use App\Components\Helper1;
use App\Http\ControllerApi\EventInfoControllerApi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use LadLib\Common\cstring2;
use LadLib\Common\Database\MetaOfTableInDb;
use LadLib\Common\UrlHelper1;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class EventAndUser_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/event-and-user';

    protected static $web_url_admin = '/admin/event-and-user';

    protected static $api_url_member = '/api/member-event-and-user';

    protected static $web_url_member = '/member/event-and-user';

    public static $titleMeta = 'Sự kiện và Thành viên tham gia';

    //public static $folderParentClass = EventAndUserFolderTbl::class;
    public static $modelClass = EventAndUser::class;

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
            //EventAndUser edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }
        if ($field == 'parent_extra' || $field == 'parent_all') {
            $objMeta->dataType = DEF_DATA_TYPE_TREE_MULTI_SELECT;
            $objMeta->join_api = '/api/need_define';
            //            $objMeta->join_func = 'App\Models\EventAndUserFolderTbl::joinFuncPathNameFullTree';
        }

        if ($field == 'parent_id') {
            $objMeta->dataType = DEF_DATA_TYPE_TREE_SELECT;
            $objMeta->join_api = '/api/need_define';
            //            $objMeta->join_func = 'App\Models\EventAndUserFolderTbl::joinFuncPathNameFullTree';
        }

        if ($field == 'signature') {
            $objMeta->dataType = DEF_DATA_TYPE_IS_ONE_IMAGE_BROWSE;
            //            $objMeta->join_func = 'App\Models\EventAndUserFolderTbl::joinFuncPathNameFullTree';
        }

        if (! $objMeta->dataType) {
            if ($ret = parent::getHardCodeMetaObj($field)) {
                return $ret;
            }
        }

        return $objMeta;
    }


    public function executeBeforeIndex($param = null)
    {
        //Tìm các id của EventInfo được tạo bở userid này, sau đó
        $user_id = getCurrentUserId();
        $mmEv = EventInfo::where('user_id', $user_id)->get();
        $mmEvId = [];
        foreach ($mmEv as $ev) {
            //ở EventAndUser, hãy SET user_id này cho mọi EventAndUser có các event_id vừa tìm được, nếu userid khác
//            EventAndUser::where('event_id', $ev->id)->where("user_id",'!=', $user_id)->update(['user_id' => $user_id]);
            EventAndUser::where('event_id', $ev->id)->update(['user_id' => $user_id]);
        }
    }

    function getSqlOrJoinExtraIndex(\Illuminate\Database\Eloquent\Builder &$x = null, $getSelect = 0)
    {
        if(Helper1::isMemberModule()){
            $mEventId = EventInfo::getEventIdListInDeparmentOfUser(getCurrentUserId());
            $x->whereIn('event_id',  $mEventId);
        }

        return $x->leftJoin('event_infos', 'event_id', '=', 'event_infos.id')
            ->leftJoin('event_user_infos', 'user_event_id', '=', 'event_user_infos.id')
            ->leftJoin('event_user_groups', 'event_user_infos.parent_id', '=', 'event_user_groups.id')
            ->addSelect([
                'event_user_infos.email AS _email',
                'event_infos.name as _event_name',
                'event_user_infos.first_name as _first_name',
                'event_user_infos.last_name as _last_name',
                'event_user_groups.name as _group_name',
            ]);
    }

    function getMapJoinFieldAlias()
    {
        return [
            '_event_name'=>'event_infos.name',
            '_email'=>'event_user_infos.email',
            '_first_name'=>'event_user_infos.first_name',
            '_last_name'=>'event_user_infos.last_name',
            '_organization'=>'event_user_infos.organization',
            '_group_name'=>'event_user_groups.name',
        ];
    }

    public function getFullSearchJoinField()
    {
        return [
            'event_user_infos.first_name'  => "like",
            'event_user_infos.last_name'  => "like",
            'event_user_infos.organization'  => "like",
            'event_user_infos.email'   => "like",
            'event_user_groups.name'   => "like",
        ];
    }

    public function _email($obj, $val, $field)
    {
        return $val;
    }

    public function _event_name($obj, $val, $field)
    {
        return $val;
    }

    public function _group_name($obj, $val, $field)
    {
        return $val;
    }

    function _signature($obj, $val, $field)
    {
        return Helper1::imageShow1($obj, $val, $field);
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

        $ret .= "<a target='_blank' href='/$module/event-info/edit/$obj->event_id?mail_to_send=$objU->email'>
<button type='button' class='btn btn-primary btn-sm send_tin_btn ml-1 my-1'> Gửi Tin </button></a>";
        $ret .= ' <DIV class="img_qr_code" style="height: 101px; '.$display.'"><img style="width: 100px" src="'.$img.'"></DIV>';
        $ret .= '</div>';

        return $ret;
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

    public function _id($obj, $val, $field)
    {
        return;

    }

    public function extraCssInclude()
    {
        ?>
        <style>
            .send_tin_btn {
                position: absolute;
                top: -35px;
                right: 5px;
                display: none;
            }
            /*            Hover qua cai  nay thi show cai khac :*/
            div[data-table-field='user_event_id']:hover .send_tin_btn{
                display: block;
            }

            /* Style cho filter select - mặc định */
            #filter_select {
                font-size: 80%;

                color: #000;
                border: 1px solid #ced4da;
                transition: all 0.3s ease;
            }

            /* Khi select có giá trị khác rỗng */
            #filter_select.active-filter {
                font-size: 75%;
                font-weight: bold;
                color: red;
                border: 2px solid red;
            }

            <?php
            $key = EventAndUser_Meta::getSearchKeyFromField('event_id');
            if(request($key)){
                ?>
                div.cellHeader.event_id{
                    display: none;
                }
                div[data-table-field=event_id]{
                    display: none!important;
                }

                <?php
            }
            ?>

        </style>

    <?php
    if($evid = request('seby_s4')){
    ?>

        <style>
            div.cellHeader.event_id{
                display: none;
            }
            input.input_value_to_post.signature{
                display: none;
            }
            div[data-table-field='event_id']{
                display: none!important;
            }
        </style>
        <?php
        }
    }

    function extraContentIndexButton1($obj = null, $x = null, $y = null)
    {

        //        <button title="Chọn các thành viên dưới đây để in mã QR" class="btn btn-sm btn-primary mb-3" id="print_qr_list"> In mã QR</button>
        ?>
        <a title="Chọn các thành viên dưới đây để in mã QR" data-code-pos="ppp1665645663340" href="#"
           style=""
           id="print_qr_list"
           class="btn btn-outline-primary btn-sm float-right mt-2 ml-3">
            <i  class="fa fa-print"></i> In QR
        </a>

        <button id="hide_qr_code" class="btn btn-outline-primary btn-sm float-right mt-2 ml-2"> QR </button>

        <?php
        $clinkClear =    UrlHelper1::setUrlParamArray(null ,['seoby_s14' =>null, 'seby_s14' => null ]);
        $clinkXacNhan =    UrlHelper1::setUrlParamArray(null ,['seoby_s14' => 'gt', 'seby_s14' => 1 ]);
        $clinkChuaXacNhan =    UrlHelper1::setUrlParamArray(null,['seoby_s14' => "N", 'seby_s14' => 'confirm_join_at_null' ]);

        $currentFilter = request('seby_s14', '');
        ?>

        <select id="filter_select" class="form-control form-control-sm float-right mt-2 mr-2" style="width: 110px;" onchange="handleFilterChange(this)">
            <option value="">- Lọc nhanh -</option>
            <option value="1" <?php echo ($currentFilter === '1' || $currentFilter === 'gt') ? 'selected' : ''; ?>>- Đã Xác nhận</option>
            <option value="confirm_join_at_null" <?php echo ($currentFilter === 'confirm_join_at_null') ? 'selected' : ''; ?>>- Chưa Xác nhận</option>
        </select>

        <script>
        function handleFilterChange(selectElement) {
            let value = selectElement.value;
            let url = '';

            // Cập nhật class active-filter khi thay đổi
            updateFilterSelectClass(selectElement);

            if (value === '') {
                url = '<?php echo $clinkClear ?>';
            } else if (value === 'confirm_join_at_null') {
                url = '<?php echo $clinkChuaXacNhan ?>';
            } else if (value === '1') {
                url = '<?php echo $clinkXacNhan ?>';
            }

            if (url) {
                window.location.href = url;
            }
        }

        function updateFilterSelectClass(selectElement) {
            let value = selectElement.value;
            console.log('Checking filter value:', value); // Debug

            if (value === '' || value === null) {
                // Loại bỏ class active-filter nếu value rỗng
                selectElement.classList.remove('active-filter');
                console.log('Removed active-filter class');
            } else {
                // Thêm class active-filter nếu có giá trị
                selectElement.classList.add('active-filter');
                console.log('Added active-filter class for value:', value);
            }
        }

        // Khởi tạo class khi load trang
        document.addEventListener('DOMContentLoaded', function() {
            let filterSelect = document.getElementById('filter_select');
            if (filterSelect) {
                console.log('Filter select found, initializing...');
                console.log('Current value:', filterSelect.value);
                updateFilterSelectClass(filterSelect);
            }
        });
        </script>


        <?php

    }

    public function getSqlOrJoinExtraEdit(\Illuminate\Database\Eloquent\Builder &$x = null, $params = null)
    {
        //Kiem tra xem User hien tai co quyen khong:
        EventInfo::checkEventBelongUser($params['id'], self::$modelClass);
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

    public function extraJsInclude()
    {
        ?>

        <!-- SheetJS library for Excel export -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

        <script>
            function checkImage(url, successCallback, errorCallback) {
                let img = new Image();
                img.onload = function()  {
                    successCallback(url,img);
                };
                img.onerror = function() {
                    errorCallback(url,img);
                };
                img.src = url;
            }


            $(function () {

                $("#export_to_ecxel").on('click',function () {
                    console.log("🔍 Starting Excel export...");

                    // ===== BƯỚC 1: LẤY HEADERS TỪ .divTable2Row.divTable2Heading1 =====
                    let headerRow = document.querySelector('.divTable2Row.divTable2Heading1');
                    if (!headerRow) {
                        alert("Không tìm thấy header row (.divTable2Row.divTable2Heading1)");
                        return false;
                    }

                    let headerCells = headerRow.querySelectorAll('.divTable2Cell');
                    let headers = [];

                    headerCells.forEach((cell, index) => {
                        let headerText = '';

                        // Lấy text từ link nếu có, không thì lấy textContent
                        let linkElement = cell.querySelector('a');
                        if (linkElement) {
                            headerText = linkElement.textContent || linkElement.innerText || '';
                        } else {
                            headerText = cell.textContent || cell.innerText || '';
                        }

                        headerText = headerText.trim();

                        // Bỏ qua cột checkbox và action
                        if (headerText && headerText !== '' &&
                            !headerText.toLowerCase().includes('select') &&
                            headerText.toLowerCase() !== 'action') {
                            headers.push(headerText);
                            console.log(`Header ${headers.length}: "${headerText}"`);
                        }
                    });

                    console.log("📋 Final headers:", headers);

                    // ===== BƯỚC 2: XỬ LÝ DỮ LIỆU ROWS =====
                    let exportData = [];

                    // Tìm tất cả các hàng dữ liệu (loại trừ header)
                    let dataRows = document.querySelectorAll('.divTable2Row:not(.divTable2Heading1)');
                    console.log(`Found ${dataRows.length} data rows`);

                    if (dataRows.length === 0) {
                        alert("Không tìm thấy dữ liệu để export.");
                        return false;
                    }

                    dataRows.forEach((row, rowIndex) => {
                        let rowData = new Array(headers.length).fill('');
                        let dataCells = row.querySelectorAll('.divTable2Cell');
                        let headerIndex = 0; // Index cho headers array

                        console.log(`\n=== Processing Row ${rowIndex + 1} ===`);

                        dataCells.forEach((cell, cellIndex) => {
                            // Bỏ qua cột checkbox (đầu tiên) và action (thứ 2)
                            if (cellIndex <= 1) {
                                return; // Skip checkbox và action columns
                            }

                            // Map với header index (trừ đi 2 cột đã skip)
                            let currentHeaderIndex = headerIndex;

                            if (currentHeaderIndex >= headers.length) {
                                return; // Tránh vượt quá số headers
                            }

                            let cellValue = '';

                            // Kiểm tra xem có span.uinfo_print không (cột Thành viên)
                            let uinfoPrint = cell.querySelector('span.uinfo_print');
                            if (uinfoPrint) {
                                // Strip HTML tags để lấy plain text
                                let tempDiv = document.createElement('div');
                                tempDiv.innerHTML = uinfoPrint.innerHTML;
                                cellValue = (tempDiv.textContent || tempDiv.innerText || '').trim();
                                console.log(`  Found uinfo_print in header "${headers[currentHeaderIndex]}": "${cellValue.substring(0, 50)}..."`);
                            }

                            // Kiểm tra input.input_value_to_post
                            let input = cell.querySelector('input.input_value_to_post');
                            if (input) {
                                let inputValue = input.value || '';
                                let dataField = input.getAttribute('data-field') || '';

                                // Nếu chưa có value từ uinfo_print, lấy từ input
                                if (!cellValue) {
                                    cellValue = inputValue;
                                }

                                console.log(`  Input in header "${headers[currentHeaderIndex]}": field="${dataField}", value="${inputValue}"`);
                            }

                            // Kiểm tra textarea.input_value_to_post (cho ghi chú)
                            let textarea = cell.querySelector('textarea.input_value_to_post');
                            if (textarea) {
                                let textareaValue = textarea.value || '';
                                if (!cellValue) {
                                    cellValue = textareaValue;
                                }
                                console.log(`  Textarea in header "${headers[currentHeaderIndex]}": value="${textareaValue}"`);
                            }

                            // Kiểm tra div.full_html_field (cho email)
                            let fullHtmlField = cell.querySelector('div.full_html_field');
                            if (fullHtmlField) {
                                let divValue = (fullHtmlField.textContent || fullHtmlField.innerText || '').trim();
                                if (!cellValue) {
                                    cellValue = divValue;
                                }
                                console.log(`  Full HTML field in header "${headers[currentHeaderIndex]}": value="${divValue}"`);
                            }

                            // Nếu vẫn chưa có value, lấy text content của cell
                            if (!cellValue) {
                                let cellText = (cell.textContent || cell.innerText || '').trim();
                                // Loại bỏ text của button và link
                                let buttons = cell.querySelectorAll('button, a');
                                buttons.forEach(btn => {
                                    let btnText = (btn.textContent || btn.innerText || '').trim();
                                    cellText = cellText.replace(btnText, '');
                                });
                                cellValue = cellText.trim();
                            }

                            // Gán vào row data
                            rowData[currentHeaderIndex] = cellValue;
                            console.log(`  ✅ Set header "${headers[currentHeaderIndex]}" = "${cellValue}"`);

                            headerIndex++;
                        });

                        // Chỉ thêm row nếu có dữ liệu
                        if (rowData.some(cell => cell && cell.toString().trim())) {
                            exportData.push(rowData);
                            console.log(`Row ${rowIndex + 1} final data:`, rowData);
                        }
                    });

                    if (exportData.length === 0) {
                        alert("Không có dữ liệu để export.");
                        return false;
                    }

                    console.log(`📊 Exporting ${exportData.length} rows to Excel`);

                    // ===== BƯỚC 3: TẠO EXCEL FILE =====
                    try {
                        // Kiểm tra XLSX library
                        if (typeof XLSX === 'undefined') {
                            console.warn("XLSX library not found, using fallback CSV export");
                            exportToCSV([headers, ...exportData]);
                            return false;
                        }

                        // Tạo Excel file
                        let ws = XLSX.utils.aoa_to_sheet([headers, ...exportData]);
                        let wb = XLSX.utils.book_new();
                        XLSX.utils.book_append_sheet(wb, ws, "Event Data");

                        // Tạo tên file với timestamp
                        let now = new Date();
                        let timestamp = now.getFullYear() +
                                      (now.getMonth() + 1).toString().padStart(2, '0') +
                                      now.getDate().toString().padStart(2, '0') + '_' +
                                      now.getHours().toString().padStart(2, '0') +
                                      now.getMinutes().toString().padStart(2, '0');
                        let filename = `event_export_${timestamp}.xlsx`;

                        // Download file
                        XLSX.writeFile(wb, filename);

                        alert(`✅ Đã export ${exportData.length} dòng dữ liệu thành công!\nFile: ${filename}\nCột: ${headers.length}`);

                    } catch (error) {
                        console.error("Error creating Excel file:", error);
                        console.warn("Fallback to CSV export");
                        exportToCSV([headers, ...exportData]);
                    }

                    // ===== HÀM FALLBACK EXPORT CSV =====
                    function exportToCSV(data) {
                        let csvContent = data.map(row =>
                            row.map(cell => `"${(cell || '').toString().replace(/"/g, '""')}"`).join(',')
                        ).join('\n');

                        let blob = new Blob(['\uFEFF' + csvContent], { type: 'text/csv;charset=utf-8;' });
                        let link = document.createElement('a');

                        if (link.download !== undefined) {
                            let now = new Date();
                            let timestamp = now.getFullYear() +
                                          (now.getMonth() + 1).toString().padStart(2, '0') +
                                          now.getDate().toString().padStart(2, '0') + '_' +
                                          now.getHours().toString().padStart(2, '0') +
                                          now.getMinutes().toString().padStart(2, '0');

                            let url = URL.createObjectURL(blob);
                            link.setAttribute('href', url);
                            link.setAttribute('download', `event_export_${timestamp}.csv`);
                            link.style.visibility = 'hidden';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);

                            alert(`✅ Đã export ${exportData.length} dòng dữ liệu thành công!\nFile: event_export_${timestamp}.csv\nCột: ${headers.length}`);
                        }
                    }

                    //Chăn chặn sự kiện mặc định của nút bấm
                    return false;
                })

                $("#hide_qr_code").on('click', function () {
                    $(".img_qr_code").toggle();
                    //Set cookie value:
                    let isShow = $(".img_qr_code").is(":visible");
                    // document.cookie = "isShowQrCode=" + isShow;
                    console.log("Set cookie isShowQrCode = ", isShow);
                    jctool.setCookie('isShowQrCode', isShow);

                })

                function CallMultiCardA7(htmlx) {
                    let WinPrint = window.open('', '', 'left=0,top=0,width=1024,height=800,toolbar=1,scrollbars=1,status=0');
                    WinPrint.document.write('<html><head><title>In Danh sách Thẻ *** Select All copy sang Docx, hoặc Ctrl + P để in ra PDF hoặc Máy in </title></head>');
                    // WinPrint.document.write('<style>@page {size: A6 landscape;margin: 1%;}</style>');
                    // WinPrint.document.write('<body style="font-family:verdana; font-size:14px;width:370px;height:270px:" >');
                    WinPrint.document.write('<script src="/adminlte/plugins/jquery/jquery.min.js"></\script>');
                    // WinPrint.document.write('<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></\script>');
                    // WinPrint.document.write('<script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs@gh-pages/qrcode.min.js"></\script>');
                    WinPrint.document.write('<link rel="stylesheet" href="/assert/css/print_multi_card_a7_in_a4.css?v=3">');
                    WinPrint.document.write('<style type="text/css">  span.uinfo_print {font-size: 80%} </style>');
                    WinPrint.document.write('<body style="" >');
                    WinPrint.document.write(htmlx);
                    // WinPrint.document.write('<script src="/template/shop1/js/print_orders.js"></\script>');
                    WinPrint.document.write('</body></html>');

                    //Nếu ko có ảnh thì set size height = 0
                    //Tìm tất cả các phần tử div có class là div_card
                    WinPrint.document.write(`<script>
let divElements = document.querySelectorAll('div.div_card');
// Duyệt qua từng phần tử div
divElements.forEach(divElement => {
// Tìm tất cả các phần tử img bên trong div
let imgElements = divElement.querySelectorAll('img');
// Duyệt qua từng phần tử img
imgElements.forEach(imgElement => {
    // Tạo một đối tượng Image mới
    let img = new Image();
    // Đặt sự kiện onload và onerror cho đối tượng Image
    img.onload = function() {
        // Hình ảnh tải thành công, không làm gì cả
    };
    img.onerror = function() {
        // Hình ảnh không tải được, đặt chiều cao của div bằng 0
        divElement.style.height = '5mm';
    };
    // Đặt thuộc tính src của đối tượng Image bằng URL của phần tử img
    img.src = imgElement.src;
});
});
<\/script>`);
                    // WinPrint.document.close();
                    WinPrint.focus();

                }

                $("#print_qr_list").on('click', function () {
                    console.log("xx1");
                    let strUid = '';
                    let strEmail = '';
                    let totalSelect = 0;
                    let imgLink = [];

                    let params = new URLSearchParams(window.location.search);
                    let evid = params.get('seby_s5'); // Thay 'myParam' bằng tên tham số bạn muốn lấy

                    if(!evid){
                        alert("Hãy Chọn Sự kiện trước");
                        return;
                    }

                    console.log(evid);

                    let html = `<style>
*{
margin: 0;
padding: 0;
border: 0;
font-size: 100%;
}
.div_card1 {
border: 1px solid #eee;
text-align: center;
padding: 2mm;
margin: 2mm; display: inline-block; width: 101mm; height: 68mm;
}
.img_card1{
height: 50mm;
}

.div_card {
border: 0px solid #eee;
/*text-align: center;*/
padding: 0mm;
margin: 2mm;
display: block;
width: 200mm;
/*height: 22mm;*/
font-size: 8px;
position: relative;
}
.img_card{
display: block;
margin-top: 0;
height: 20mm;
}


</style>`
                    let tmp = 0
                    let cc = 0;
                        //                    echo "<br/>\n xxx $idf ";
                    $(".select_one_check").each(function () {

                        if ($(this).is(":checked")) {
                            let dtid = $(this).attr('data-id')
                            console.log(" ID = ", dtid);
                            if (dtid) {
                                totalSelect++;
                                $("input[data-id=" + $(this).attr('data-id') + "][data-field=user_event_id]").each(function () {
                                    let uid = $(this).val()

                                    let uname = $('#user_info_' + uid).html();

                                    console.log(" Found ", $(this).val());
                                    // strEmail += $(this).val() + ','
                                    let linkImg = `/images/code_gen/ncbd-event-${evid}-${uid}.png`

                                    let that = this;
                                    // Sử dụng hàm:

                                    console.log("linkImg = ", linkImg);
                                    html += `<div class='div_card' style=''>
<div style="display: inline-block; text-align:center">
<img class="img_card" src='${linkImg}' style="width: auto" />
<span data-code-pos='' style="color: #ccc; text-align: left">${uid} - ${evid}</span>
</div>
<div data-code-pos='ppp17292236802151' style='margin-top: 1mm; color: '>  ${uname}</div>
</div>
`
                                })
                            }
                        }
                    })

                    if (!totalSelect) {
                        alert("Hãy chọn Check box Thành viên bên dưới muốn thực hiện");
                        return;
                    }

                    if (this.id == 'print_qr_list'){

                        CallMultiCardA7(html);
                    }

                    console.log(" strUid ", strUid);
                    console.log(" strEmail ", strEmail);
                })

                // ===== DEBUG FUNCTIONS =====

                // DEBUG FUNCTION: Test Excel export structure
                window.debugExcelStructure = function() {
                    console.log("=== DEBUG EXCEL STRUCTURE ===");

                    // Check header row
                    let headerRow = document.querySelector('.divTable2Row.divTable2Heading1');
                    if (headerRow) {
                        console.log("✅ Header row found");
                        let headerCells = headerRow.querySelectorAll('.divTable2Cell');
                        console.log(`Headers (${headerCells.length} cells):`);
                        headerCells.forEach((cell, index) => {
                            let text = (cell.textContent || cell.innerText || '').trim();
                            let linkText = '';
                            let linkElement = cell.querySelector('a');
                            if (linkElement) {
                                linkText = ' -> Link: "' + (linkElement.textContent || linkElement.innerText || '').trim() + '"';
                            }
                            console.log(`  ${index}: "${text}"${linkText}`);
                        });
                    } else {
                        console.log("❌ Header row NOT found");
                    }

                    // Check data rows
                    let dataRows = document.querySelectorAll('.divTable2Row:not(.divTable2Heading1)');
                    console.log(`\n📊 Found ${dataRows.length} data rows`);

                    if (dataRows.length > 0) {
                        let firstRow = dataRows[0];
                        let cells = firstRow.querySelectorAll('.divTable2Cell');
                        console.log(`First row has ${cells.length} cells:`);

                        cells.forEach((cell, index) => {
                            let input = cell.querySelector('input.input_value_to_post');
                            let textarea = cell.querySelector('textarea.input_value_to_post');
                            let uinfoPrint = cell.querySelector('span.uinfo_print');
                            let fullHtml = cell.querySelector('div.full_html_field');

                            console.log(`  Cell ${index}:`);
                            if (input) {
                                console.log(`    Input: field="${input.getAttribute('data-field')}", value="${input.value}"`);
                            }
                            if (textarea) {
                                console.log(`    Textarea: field="${textarea.getAttribute('data-field')}", value="${textarea.value}"`);
                            }
                            if (uinfoPrint) {
                                let tempDiv = document.createElement('div');
                                tempDiv.innerHTML = uinfoPrint.innerHTML;
                                let plainText = (tempDiv.textContent || tempDiv.innerText || '').trim();
                                console.log(`    UInfo: "${plainText.substring(0, 50)}..."`);
                            }
                            if (fullHtml) {
                                console.log(`    FullHTML: "${fullHtml.textContent?.substring(0, 50)}..."`);
                            }
                        });
                    }
                };

                // TEST FUNCTION: Test export with small data
                window.testExcelExport = function() {
                    console.log("=== TEST EXCEL EXPORT ===");

                    // Manually trigger the export function
                    let exportButton = document.querySelector('#export_to_ecxel');
                    if (exportButton) {
                        exportButton.click();
                    } else {
                        console.log("❌ Export button not found");
                    }
                };

            })

        </script>

        <?php
    }
}
