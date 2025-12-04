<?php

namespace App\Models;

use App\Components\Helper1;
use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param null $objData
 */
class EventUserInfo_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/event-user-info';

    protected static $web_url_admin = '/admin/event-user-info';

    protected static $api_url_member = '/api/member-event-user-info';

    protected static $web_url_member = '/member/event-user-info';

    public static $titleMeta = 'Quản lý Khách tham gia sự kiện';

    public static $folderParentClass = EventUserGroup::class;

    public static $modelClass = EventUserInfo::class;

    public static $allowAdminShowTree = 1;

    public static $titleAfterFolderButton = "Danh bạ";

    static $limitRecord = 50;

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();
        if ($field == 'status') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }

        if ($field == 'parent_extra' || $field == 'parent_all') {
            $objMeta->dataType = DEF_DATA_TYPE_TREE_MULTI_SELECT;
            $objMeta->join_api = '/api/event-user-group';
            //            $objMeta->join_func = 'App\Models\DemoFolderTbl::joinFuncPathNameFullTree';
        }

        if ($field == 'parent_id') {
            $objMeta->dataType = DEF_DATA_TYPE_TREE_SELECT;
            $objMeta->join_api = '/api/event-user-group';
            //            $objMeta->join_func = 'App\Models\DemoFolderTbl::joinFuncPathNameFullTree';
        }

        if ($field == 'image_list') {
            $objMeta->dataType = DEF_DATA_TYPE_IS_MULTI_IMAGE_BROWSE;
        }

        if ($field == 'signature') {
            $objMeta->dataType = DEF_DATA_TYPE_IS_MULTI_IMAGE_BROWSE;
        }

        if ($field == 'guide_admin' || $field == 'extra_info') {
            $objMeta->dataType = DEF_DATA_TYPE_TEXT_AREA;
        }

        if ($field == 'language' || $field == 'bank_name_text') {
            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;
        }

        if ($field == 'note') {
            $objMeta->dataType = DEF_DATA_TYPE_TEXT_AREA;
        }

        if ($field == 'gender' || $field == 'payment_type') {
            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;
        }

        return $objMeta;
    }

    function _email($obj, $val, $field){

        return $obj->bank_name_text;
    }

    function _bank_name_text($obj, $val, $field){
        $meta = new EventRegister_Meta();

        return $meta->_bank_name_text($obj, $val, $field);
    }

    function _gender()
    {
        $mm = [
            0 => '-Chọn-',
            1 => 'Nam',
            2 => 'Nữ',
        ];
        return $mm;
    }

    function _payment_type()
    {
        $mm = [
            0 => '-Chọn-',
            'trong_nuoc' => 'Trong nước',
            'nuoc_ngoai' => 'Nước ngoài',
        ];
        return $mm;
    }

    public function getSqlOrJoinExtraIndex(\Illuminate\Database\Eloquent\Builder &$x = null, $getSelect = 0) {

        if(isAdminCookie())
        if($evId = request('_event_id_')){

            $mid = EventAndUser::where('event_id', $evId)->pluck('user_event_id');

//            dump((array_values($mid->toArray())));
            return $x->whereIn('id',$mid);
//            return $x->leftJoin('event_and_users', 'event_id', '=', 'event_infos.id')
//                ->addSelect([
//                    'event_infos.name AS _name',
//                ]);

        }

    }

    public function extraContentIndexButton1($v1 = null, $v2 = null, $v3 = null)
    {
        ?>

<!--        <button class="btn btn-primary btn-sm float-right mt-2 ml-2" id="export_thanh_toan_event"> Export Thanh toán </button>-->
        <?php
    }

    public function getArraySelectNumber()
    {


        return [5, 10, 20, 50, 100];
    }
    public function getFullSearchJoinField()
    {

        return [
            'first_name'  => "like",
            'last_name'  => "like",
            'email'  => "like",
            'phone'  => "like",
            'organization'  => "like",
            'designation'  => "like",
        ];

    }

    function getFieldToImportExcel()
    {
        return [
            'title' => ['name' => 'Danh xưng', 'size' => 10],
            'first_name' => ['name' => 'Tên', 'size' => 10],
            'last_name' => ['name' => 'Họ & đệm', 'size' => 15],
            'gender' => ['name' => '1=Nam/2=Nữ', 'size' => 15],
            'email' => ['name' => 'Email', 'size' => 20],
            'phone' => ['name' => 'Điện thoại', 'size' => 15],
            'address' => ['name' => 'Địa chỉ', 'size' => 30],
            'organization' => ['name' => 'Tổ chức', 'size' => 30],
            'note' => ['name' => 'Ghi chú', 'size' => 50],
        ];
    }

    public function beforeInsertDb(&$getPost = null, $post = null)
    {

        if ('__add_to_event' == ($getPost['__cmd_post'] ?? '')) {
            if(isDebugIp()){
//                die("xxxxx1");
            }
//            unset($getPost['sub_event_list']);
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($getPost);
//            echo "</pre>";
//            die();
        } else
            return;

        //neu co user, thi gan vao su kien luon:
        $evId = $getPost['__cmd_param'] ?? '';
        if (!$evId || !is_numeric($evId))
            return;


        //Kiem tra neu email da co trong DB thi ko cho insert
        //Dua luon user vaof EventAndUser
        $email = ($getPost['email'] ?? '');
        if (!$email)
            return;
        if (!($obj = EventUserInfo::where('email', $email)->first()))
            return;

        $evx = EventInfo::find($evId);
        if (!$evx)
            loi("Không tìm thấy sự kiện để thêm user vao: $evId");
        //Tim xem co user va event id trong EventAndUser chua
        $evu = EventAndUser::where('user_event_id', $obj->id)->where('event_id', $evId)->first();
        if (!$evu) {
            $evu = new EventAndUser();
            $evu->user_event_id = $obj->id;
            $evu->event_id = $evId;
            $evu->addLog("Quick Add User To Event");
            $evu->save();
            loi("\n- User ($email)\nđã tồn tại và Vừa được thêm vào sự kiện:\n\n- $evx->name\n\n- Bấm F5 để nhập Thành viên khác vào Sự kiện!\n\n");
        }
        loi("\n- User ($email)\nđã tồn tại và đã được thêm vào sự kiện ($evu->created_at) :\n\n- $evx->name\n\n- Bấm F5 để nhập Thành viên khác vào Sự kiện!\n\n");
    }

    public function afterInsertApi($obj, $getPost = null, $post = null)
    {
//        if(isIPDebug())
        {
            if ('__add_to_event' == ($getPost['__cmd_post'] ?? '')) {
                $evId = $getPost['__cmd_param'] ?? 0;
                if (!$evId || !is_numeric($evId))
                    return;
                $evx = EventInfo::find($evId);
                if (!$evx)
                    loi("Không tìm thấy sự kiện de them user vao: $evId");
                //Tim xem co user va event id trong EventAndUser chua
                $evu = EventAndUser::where('user_event_id', $obj->id)->where('event_id', $evId)->first();
                if (!$evu) {
                    $evu = new EventAndUser();
                    $evu->user_event_id = $obj->id;
                    $evu->event_id = $evId;
                    $evu->addLog("Quick Add User To Event");
                    $evu->save();
//                    die("Them user $obj->id nay vao su kien: $evId");
                }

                if($post['___sub_event_id_post'] ?? ''){
                    $subEventList = $post['___sub_event_id_post'];
                    foreach ($subEventList as $evId){
                        $evx = EventInfo::find($evId);
                        if (!$evx)
                            continue;
                        //Tim xem co user va event id trong EventAndUser chua
                        $evu = EventAndUser::where('user_event_id', $obj->id)->where('event_id', $evId)->first();
                        if (!$evu) {
                            $evu = new EventAndUser();
                            $evu->user_event_id = $obj->id;
                            $evu->event_id = $evId;
                            $evu->addLog("Quick Add User To SubEvent");
                            $evu->save();
                        }
                    }
                }
            }
        }
    }

    public function getExtraDataEditFieldNameX1($field)
    {
        $html = '';
        if ($field == 'image_list') {
            $html = "<button type='button' id='re_learn' class='btn btn-sm btn-outline-info' title='Nhận dạng ảnh mới up vào ImageList, xong cần ấn nút Save - Ghi lại'>
Cập nhật nhận dạng </button> <a href=''></a> ";
        }

        return $html;

    }

    public function extraJsIncludeEdit($objData = null)
    {

        require_once "/var/www/html/public/tool1/_site/event_mng/js_event.php";
?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                if (document.getElementById('re_learn')) {
                    console.log(" Relearn...");
                    document.getElementById('re_learn').addEventListener('click', function () {
                        // if (confirm("Bạn có chắc muốn học lại?"))
                        {
                            reLearnFace();
                        }
                    });
                }



            });
        </script>
  <?php
    }



    public function extraJsInclude()
    {
        ?>
        <script src="/admins/upload_file.js"></script>
        <script>

            $("#export_thanh_toan_event").on('click', function () {

                return;

                //Kiểm tra trên URL xem có tham số _event_id_ không, nếu ko có thì báo lỗi:
                let event_id = jctool.getUrlParam('_event_id_');
                if (!event_id) {
                    alert("Vui lòng chọn Sự kiện trước khi xuất file thanh toán!");
                    return;
                }

                //Post vao /tool1/mytree/export_excel.php với tham số event_id
                //Và mở file trả về trong tab mới
                window.open("/tool1/mytree/export_excel_event_user.php?_event_id_=" + event_id, '_blank');

                // $.post("/tool1/mytree/export_excel_event_user.php", { _event_id: event_id }, function (data) {
                //     console.log("Export data: ", data);
                // });

                // alert("Đang phát triển");
            });

            $("#inport_from_excel").on('click', function () {
                $("#zone_upload_event").toggle();
            })


            let objUpload = new clsUploadV2()

            objUpload.url_server = '/api/member-file/upload';
            objUpload.bind_selector_upload = 'upload_admin_zone';
            // objUpload.bind_selector_result = 'result-area-upload';
            // objUpload.bind_div_upload_status_all = 'div_upload_status_all';

            objUpload.upload_done_call_function = 'UploadDone_XuLyImportExcel'
            objUpload.upload_queue = 0;
            objUpload.uploading = 0;
            objUpload.upload_done = 0;
            objUpload.upload_total = 0;
            objUpload.upload_error = 0;
            objUpload.maxFileCC = 2;
            objUpload.bearerToken = jctool.getCookie('_tglx863516839');
            objUpload.maxSizeUpload = <?php echo \App\Models\SiteMng::getMaxSizeUpload()?>;

            let sField = '<?php echo $searchField = \App\Models\FileUpload_Meta::getSearchKeyFromField('parent_id'); ?>';
            if (jctool.getUrlParam(sField))
                objUpload.set_parent_id = jctool.getUrlParam(sField);

            objUpload.mFileUpload = [];


            $(function () {
                //    objUpload.initUpload()
            })

            function UploadDone_XuLyImportExcel(ret, objUpload) {
                console.log(" upload_done_call_function  xuLyImportExcel - RET from server: ", ret);

                let retObj;
                if (typeof ret == 'object')
                    retObj = ret;
                else
                    retObj = JSON.parse(ret);

                console.log(" JSONx  ", retObj);

                let idFile = retObj?.payload?.id;
                if (!idFile) {
                    alert("Không tìm thấy ID file upload? ");
                    return;
                }

                let url = '/api/member-file/importExcel?idf' + idFile;
                let user_token = jctool.getCookie('_tglx863516839');

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + user_token,
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        hideWaittingIcon();
                        console.log("Data ret: ", data);
                        if (data.payload) {
                            console.log(" get all Data ", data.payload);
                            alert(data.payload);
                        } else {
                            alert("Có lỗi xảy ra: \n" + JSON.stringify(data));
                        }
                    })
                    .catch(error => {
                        hideWaittingIcon();
                        console.log(" DATAx ", error);
                        alert('Error call api: ' + "\n" + error.message);
                    });


            }

        </script>


        <?php

    }

    public function extraCssIncludeEdit()
    {
    ?>
        <style>
            .extra_edit0 {
                margin-bottom: 20px;
                background-color: white;
                padding: 20px 20px 20px 20px;

            }

        </style>
        <?php
    }

    public function extraCssInclude()
    {
        ?>

        <style>

            select[data-field="bank_name_text"] {
                max-width: 30px;
            }

            div[data-table-field='title'] {
                min-width: 60px;
            }

            div[data-table-field='first_name'] {
                min-width: 100px;
            }

            div[data-table-field='last_name'] {
                min-width: 80px;
            }

            div[data-table-field='email'] {
                min-width: 180px;
            }

            div[data-table-field='phone'] {
                min-width: 80px;
            }

            div[data-table-field='image_list'] {
                max-width: 80px;
            }

            input[data-field=image_list], input.signature {
                display: none
            }

            .divTable2Cell .all_node_name .img_zone img {
                max-width: 50px;
            }
        </style>
        <?php
    }

    public function _language($objData = null, $value = null, $field = null)
    {

        /**
         * // Với trường hợp trong DB ghi luôn value mà ko phải Index, thì có thể kiểu $ret [$month] = $month;
         * $ret[0] = "---";
         * $mm = ['2023-01', '2023-02'];
         * foreach ($mm AS $month){
         * $ret [$month] = $month;
         * }
         */
        $mm = [
            0 => '-Chọn-',
            'vi' => 'Việt',
            'en' => 'Eng',
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

    public function _image_list($obj, $val, $field)
    {
        return Helper1::imageShow1($obj, $val, $field);
    }

    public function _signature($obj, $val, $field)
    {
        return Helper1::imageShow1($obj, $val, $field);
    }

    public function extraHtmlIncludeEdit0()
    {


        ?>

        <script>
            // Scroll đến input theo __focus__ parameter
            document.addEventListener('DOMContentLoaded', function() {
                const params = new URLSearchParams(window.location.search);
                const focusField = params.get('__focus__');
                
                if (focusField) {
                    const input = document.querySelector(`input[name="${focusField}"]`);
                    if (input) {
                        // Scroll đến input
                        input.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        // Focus vào input sau khi scroll
                        setTimeout(() => {
                            input.focus();
                            input.select ? input.select() : null;
                        }, 500);
                    }
                }
            });

            //Tìm div có data-namex2='email' và thêm Check Box Không có Mail, khi click vào đó thì điền
            // một email giả có dạng unixtime@localhost.com, với unixtime là thời gian hiện tại dạng giây
            window.addEventListener('load', function () {
                $("div[data-namex2='email']").append("<input type='checkbox' id='chk_no_email' style='margin-top: 5px'> <label for='chk_no_email'> &nbsp Không có Email </label>");
                $("#chk_no_email").on('click', function () {
                    if ($(this).is(":checked")) {
                        let unixtime = Math.floor(Date.now() / 1000);
                        $("input[data-field=email]").val(unixtime + "@KhongCoEmail.com");
                    } else {
                        $("input[data-field=email]").val('');
                    }
                })


                //Neu co form ID = form_save_one, thi
                //moi khi click vao .sub_event_info, se them input check vao form
                //de biet duoc su kien con nao duoc chon
                if ($("#form_save_one").length) {
                    $(".sub_event_zone").on('click', function () {
                        $("#form_save_one").find("input.tmp_event_id").remove();

                        $(".sub_event_info").each(function () {
                            let id = $(this).find('input').attr('id');
                            id = id.replace('sub_event_', '')
                            let name = $(this).find('input').attr('name');
                            let checked = $(this).find('input').prop('checked');
                            console.log(" Clicked: ", id, name, checked);
                            if (checked) {
                                $("#form_save_one").append("<input class='tmp_event_id input_value_to_post' type='hidden' " +
                                    "name='___sub_event_id_post[]' value='"+ id +"'>");
                            } else {
                            }
                        })
                    })
                }


            });

        </script>


        <?php

        if (request('__cmd_post') == '__add_to_event') {


            $evId = request('__cmd_param');
            $evx = EventInfo::find($evId);
            if (!$evx) {
                bl("Không tồn tại sự kiện này $evId? ");
                echo "<br/>\n";
                return;
            }

            echo(" <h4 class='mb-2 text-primary'> Thêm thành viên vào Sự kiện: <b> $evx->name </b></h4>");
            echo(" <div class='mb-3'>- Chú ý: để tránh trùng lặp nhập lại thành viên cũ, trước tiên chỉ nhập <b style='color: brown'> Email </b> vào bên dưới và bấm nút
 <b  style='color: brown'> Ghi lại </b>
 để kiểm tra, sau đó nhập các thông tin khác!</div>");


            $meta = new EventRegister_Meta();
            $meta->extraCssIncludeEdit();

            $meta->extraJsIncludeEdit();


            $ret = EventInfo::htmlSubEventInputCheck($evx);
            if($ret){
                echo " - Bạn có thể Chọn thêm Sự kiện con cho user này:";
                echo $ret;
            }


            return;
        }

        $uid = request('id');
        if (!$uid)
            return;
        $userInfo = EventUserInfo::find($uid);

        ?>

        <div class="p-3 mb-3" style="background-color: white; margin-top: -15px">

            <?php


            echo " <b style='color: royalblue'> <i class='fa fa-address-card'></i> $userInfo->title $userInfo->last_name  $userInfo->first_name  </b>";

            //Số sự kiện tham gia:
            $mmUe = EventAndUser::where('user_event_id', $uid)->get();
            $nEv = count($mmUe);
            $Nattend_at = $Nconfirm_join_at = $Ndeny_join_at = 0;
            echo "\n<table class='glx012 mb-2' style='margin: 10px 0px 5px 0px!important;'>";
            foreach ($mmUe as $one) {
                if ($one->attend_at) {
                    $Nattend_at++;
                }
                if ($one->confirm_join_at) {
                    $Nconfirm_join_at++;
                }
                if ($one->deny_join_at) {
                    $Ndeny_join_at++;
                }

            }

            $key = EventAndUser_Meta::getSearchKeyFromField('user_event_id');

            echo " <tr>
 <td> Số sự kiện ghi danh: </td>
 <td>
 <a target='_blank' href='/admin/event-and-user?$key=$uid'>
 <small class='badge badge-primary'> $nEv </small>
 </a>
 </td>
  </tr> ";
            echo "<tr>
<td>Số sự kiện xác nhận tham gia: </td>
<td><a target='_blank' href='/admin/event-and-user?seby_s3=$uid'>
<small class='badge  badge-info'> $Nconfirm_join_at </small>   </a>
</td>
</tr> ";
            echo "<tr>
<td> Số sự kiện tham gia chính thức:</td>
<td> <a target='_blank' href='/admin/event-and-user?seby_s3=$uid'>
<small class='badge badge-danger'> $Nattend_at </small>  </a>
</td>
</tr>";
            echo "<tr>
<td> Số sự kiện từ chối: </td>
<td>
<a target='_blank' href='/admin/event-and-user?seby_s3=$uid'>
<small class='badge badge-warning'> $Ndeny_join_at </small>
</a>
</td>
</tr>
";

            $nMailSend = EventSendInfoLog::where(['event_user_id' => $uid, 'type' => 'email'])->count();
            $nSmsSend = EventSendInfoLog::where(['event_user_id' => $uid, 'type' => 'sms'])->count();

            echo "<tr>
<td> Số Email đã gửi: </td>
<td>
<a target='_blank' href='/admin/event-send-info-log?seby_s3=$uid&seoby_s5=eq&seby_s5=email'>
<small class='badge badge-success'> $nMailSend </small>
</a>
</td>
</tr>";

            echo "<tr>
<td> Số SMS đã gửi: </td>
<td>
<a target='_blank' href='/admin/event-send-info-log?seby_s3=$uid&seoby_s5=eq&seby_s5=sms'>
<small class='badge badge-success'> $nSmsSend </small>
</a>
</td>
</tr>";
            echo "\n</table>";
            ?>


        </div>

        <?php

    }

    public function extraContentIndex1($v1 = null, $v2 = null, $v3 = null)
    {
        ?>


        <div style='margin: ; border: 0px solid #ccc' class="mb-3 bg-white px-2 py-2" data-code-pos='ppp17356059759851'>
            <?php

//            EventInfo::getHtmlSelectEvent(null);


            if ($evId = request('_add_user_to')) {
                $evx = EventInfo::find($evId);
                echo(" <i class='fa fa-info-circle'></i> Bạn đang thêm thành viên vào Sự kiện: <b style='color: royalblue'> $evx->name (Mã số: $evId) </b>
<br> Hãy chọn thành viên bằng CheckBox bên dưới và nhấn Nút thêm ở sau đây:
");
                echo "\n<div></div>";
                echo "\n<button id='open_dialog2' class='btn-sm btn-primary mt-2 '> Thêm Thành viên đã chọn vào Sự kiện </b></button> ";
                echo "\n
<a href='/admin/event-user-info/create?__cmd_post=__add_to_event&__cmd_param=$evId'>
<button type='button' class='btn-sm btn-default mt-2 ml-3'> Thêm Thành viên mới </b></button>
</a>
";

            } else {
                ?>
                <button id='open_dialog1' class='btn btn-sm btn-default mr-2'> Thêm Thành viên vào Sự kiện</button>
                <button class="btn btn-sm btn-default mr-2" id="get_email_list"> Lấy danh sách Email</button>

                <button type="button" class="btn btn-sm btn-default" id="inport_from_excel"> Nhập từ Excel</button>


                <?php

            }


            ?>
        </div>

        <div id="dynamic-select-container" class="select_to_add mb-3" data-code-pos='ppp17356059816221'>

            <div id="select_number_item"></div>

            <div class="row">
                <div class="col-md-9">
                    <select id="dynamic-select" placeholder="Choose an option..." data-code-pos='ppp17356059857431'>
                        <option value="" disabled selected> --- Chọn sự kiện để thêm ---</option> <!-- Title ban đầu -->

                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary" style=" width: 100%" id="add_items">

                        <i class="fa fa-plus"></i>

                        Thêm thành viên đã chọn vào sự kiện này</button>
                </div>

            </div>

        </div>

        <div id="dialog1_xxx" title="Thêm Thành viên vào sự kiện" data-code-pos='ppp17356059980061'
             style="display: none; padding: 0px 0px; position: relative;">

            <div style="border: 0px solid #ccc; min-height: 600px; background-color: ; border-bottom: 1px solid #eee;
padding: 20px" data-code-pos='ppp17356060033101'>
                <table style="width: 100%; height: ;  font-size: small">
                    <tr style="height: 100%">
                        <td style="height: 100%; ; vertical-align: top">
                            <b style="font-size: 120%" data-code-pos='ppp17356060010901'>
                                Chọn sự kiện:
                            </b>
                            <div style="margin-top: 10px; overflow-y: scroll; height: 95%;; padding-left: 10px">
                                <?php
                                $uid = getCurrentUserId();
                                if (\App\Components\Helper1::isMemberModule())
                                    $mm = \App\Models\EventInfo::where("user_id", $uid)->orderBy("id", 'desc')->get();
                                else
                                    $mm = EventInfo::latest('id')->get();


                                foreach ($mm as $obj1) {
                                    ?>
                                    <input class="input_check_eml" data-id="<?php echo $obj1->id ?>"
                                           id="gr_ev_sl_<?php echo $obj1->id ?>" type="checkbox"
                                           style="color: darkslateblue; display: inline"
                                    />
                                    <label style="color: darkslateblue; display: inline"
                                           for="gr_ev_sl_<?php echo $obj1->id ?>">
                                        <?php
                                        echo "#$obj1->id . $obj1->name" . " <a target='_blank' href='/admin/event-info/edit/$obj1->id'> <i style='color: gray' class='fas fa-external-link-alt'></i> </a>"
                                        ?>
                                    </label>
                                    <br>
                                    <?php
                                }
                                ?>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="position: absolute; bottom: 12px; right:20px; border-top: 0px solid #eee">
                <button class="btn btn-primary" id="add_cmd"> Thêm</button>
                <button class="btn btn-default" id="close_dialog1"> Bỏ qua</button>
            </div>
        </div>


        <div class="my-2 mb-3 px-3 py-2 pb-3" style="background-color: white; border: 0px solid #ccc; display: none; font-size: 90%"
             id="zone_upload_event">
            - <a target="_blank" href="/tool1/_site/event_mng/create_excel_file_user_info.php"> Tải file mẫu Excel, nhập Dữ liệu </a>,
            và upload lên <a href="https://drive.google.com/" target="_blank"> Google Drive </a> , Chia sẻ link đó
            Public. Sau khi nhập dữ liệu xong,
            copy & paste link vào đây, và bấm nút "Nhập" <br>
            Link Google Drive chỉ cần tạo một lần, sử dụng cho nhiều lần nhập, có thể nhập/sửa dữ liệu trên Link đó.
            <div class="form-group-sm d-flex align-items-center mt-1">
                <input type="text" placeholder="Đưa link Google Sheet (đã chia sẻ public) vào đây"
                       class="form-control form-control-sm w-50 mr-2" id="link_excel_gg"
                       value=""
                >
                <button class="btn btn-sm btn-primary" id="import_excel_gg">Nhập</button>
                <?php
                //\App\Models\FileUpload_Meta::includeUploadZoneHtmlSample("upload_admin_zone", ".xls,.xlsx,.csv");
                ?>
            </div>
        </div>
        <link rel="stylesheet" href="/admins/upload_file.css?v=1725510479">


        <script>


            function addUserToEvent(mmSelectId, mmEventCheck) {
                showWaittingIcon();
                let user_token = jctool.getCookie('_tglx863516839');

                let url = "/api/event-info/addUserToEvent";
                $.ajax({
                    url: url,
                    type: 'POST',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                    },
                    data: {mmSelectId: mmSelectId, mmEventCheck: mmEventCheck},
                    success: function (data, status) {
                        hideWaittingIcon();
                        console.log("Data ret: ", data, " \nStatus: ", status);
                        if (data.payload) {
                            console.log(" get all Data ", data.payload);
                            alert(data.payload);
                            // $("#div_list_bai_test_id").html(JSON.stringify(data.payload.data));
                        } else {
                            alert("Có lỗi xảy ra: \n", JSON.stringify(data))
                        }
                    },
                    error: function (data) {
                        hideWaittingIcon();
                        console.log(" DATAx ", data);
                        if (data.responseJSON && data.responseJSON.message)
                            alert('Error call api: ' + "\n" + data.responseJSON.message)
                        else
                            alert('Error call api: ' + "\n" + url + "\n\n" + JSON.stringify(data).substr(0, 1000));
                    }
                });
            }

            window.addEventListener('load', function () {
                let user_token = jctool.getCookie('_tglx863516839');

                $("#import_excel_gg").on('click', function () {

                    console.log("CLick import_excel_gg ");

                    let link = $("#link_excel_gg").val();

                    if (!link) {
                        alert("Hãy nhập link!")
                        return;
                    }

                    let url = '/api/event-info/importExcelUserEvent'
                    let data = {link_excel: link};
                    $.ajax({
                        url: url,
                        type: 'POST',
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                        },
                        data: data,
                        success: function (data, status) {
                            hideWaittingIcon();
                            console.log("Data ret: ", data, " \nStatus: ", status);
                            if (data.payload) {
                                console.log(" get all Data ", data.payload);
                                alert(data.message);
                                // $("#div_list_bai_test_id").html(JSON.stringify(data.payload.data));
                            } else {
                                alert("Có lỗi xảy ra: \n" + (data))
                            }
                        },
                        error: function (data) {
                            hideWaittingIcon();
                            console.log(" DATAx ", data);
                            if (data.responseJSON && data.responseJSON.message)
                                alert('Error call api: ' + "\n" + data.responseJSON.message)
                            else
                                alert('Error call api: ' + "\n" + url + "\n\n" + JSON.stringify(data).substr(0, 1000));
                        }
                    });


                });


                $(function () {

                    $("#add_cmd").on('click', function () {
                        let mmSelectId = clsTableMngJs.getSelectingCheckBox();
                        console.log(" mmSelectId = ", mmSelectId);
                        let mmEventCheck = [];
                        $("input.input_check_eml:checked").each(function () {
                            mmEventCheck.push(this.getAttribute('data-id'));
                        })
                        console.log("Add cmd , mmEvent = ", mmEventCheck);
                        addUserToEvent(mmSelectId, mmEventCheck);
                    })

                    $("#open_dialog2").on('click', function () {
                        let mmSelectId = clsTableMngJs.getSelectingCheckBox();
                        if (!mmSelectId.length) {
                            alert("Bạn chưa chọn danh sách user để thêm?\n\nHãy chọn với check box bên dưới!");
                            return;
                        }

                        if (!confirm("Bạn sẽ thêm " + mmSelectId.length + " thành viên vào Sự kiện ?")) {
                            return;
                        }

                        console.log(" mmSelectId = ", mmSelectId);
                        let mmEventCheck = [];
                        mmEventCheck.push(<?php echo $evId ?>);
                        console.log("Add cmd , mmEvent = ", mmEventCheck);
                        addUserToEvent(mmSelectId, mmEventCheck);
                    })

                    // $("#open_dialog1").on('click', function () {
                    //     let mmSelectId = clsTableMngJs.getSelectingCheckBox();
                    //     if (!mmSelectId.length) {
                    //         alert("Bạn chưa chọn danh sách user để thêm? Hãy chọn với check box!");
                    //         return;
                    //     }
                    //
                    //
                    //     $("#dialog1").dialog('open');
                    //
                    // })

                    $("#close_dialog1").on('click', function () {
                        $("#dialog1").dialog('close');
                    })

                    $("#dialog1").dialog({
                        resizable: true,
                        height: 400,
                        autoOpen: false,
                        width: 600,
                        modal: true,
                        open: function (event, ui) {
                            console.log('opened ...')
                            // showWaittingIcon();
                            // let url = "/api/quiz-test/list";
                            // $.ajax({
                            //     url: url,
                            //     type: 'GET',
                            //     beforeSend: function (xhr) {
                            //         xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                            //     },
                            //     success: function (data, status) {
                            //         hideWaittingIcon();
                            //         console.log("Data ret: ", data, " \nStatus: ", status);
                            //         if (data.payload) {
                            //             console.log(" get all Data ", data.payload.data);
                            //
                            //
                            //             // $("#div_list_bai_test_id").html(JSON.stringify(data.payload.data));
                            //         }
                            //     },
                            //     error: function (data) {
                            //         hideWaittingIcon();
                            //         console.log(" DATAx ", data);
                            //         if (data.responseJSON && data.responseJSON.message)
                            //             alert('Error call api: ' + "\n" + data.responseJSON.message)
                            //         else
                            //             alert('Error call api: ' + "\n" + url + "\n\n" + JSON.stringify(data).substr(0, 1000));
                            //     }
                            // });


                        },
                    });

                });


                $(function () {
                    $("#get_uid_list, #get_email_list").on('click', function () {
                        console.log("xx1");
                        let strUid = '';
                        let strEmail = '';
                        let totalSelect = 0;
                        $(".select_one_check").each(function () {
                            if ($(this).is(":checked")) {
                                let dtid = $(this).attr('data-id')
                                console.log(" ID = ", dtid);
                                if (dtid) {
                                    totalSelect++;
                                    $("input[data-id=" + $(this).attr('data-id') + "][data-field=email]").each(function () {
                                        strUid += dtid + ','
                                        console.log(" Found ", $(this).val());
                                        strEmail += $(this).val() + ','
                                    })
                                }
                            }
                        })

                        if (!totalSelect) {
                            alert("Hãy chọn Check box Thành viên bên dưới muốn thực hiện");
                            return;
                        }
                        if (this.id == 'get_email_list') {
                            navigator.clipboard.writeText(strEmail);
                            showToastInfoTop(" Đã copy vào clipboard danh sách uid/email: " + totalSelect)
                        }

                        console.log(" strUid ", strUid);
                        console.log(" strEmail ", strEmail);
                    })
                })

            });


        </script>


        <script>
            document.addEventListener('DOMContentLoaded', () => {

                $("#add_items").on('click', function () {
                    let select = document.getElementById('dynamic-select');
                    let selectedOption = select.options[select.selectedIndex];
                    console.log('Selected option:', selectedOption.value, selectedOption.text);

                    if(!selectedOption.value){
                        alert("Chưa chọn sự kiện!")
                        return;
                    }
                    // alert('Selected option: ' + selectedOption.text);

                    let mmSelectId = clsTableMngJs.getSelectingCheckBox();
                    console.log(" mmSelectId = ", mmSelectId);
                    let mmEventCheck = [];

                    // $("input.input_check_eml:checked").each(function () {
                    //     mmEventCheck.push(this.getAttribute('data-id'));
                    // })
                    mmEventCheck.push(selectedOption.value);
                    console.log("Add cmd , mmEvent = ", mmEventCheck);
                    addUserToEvent(mmSelectId, mmEventCheck);

                });


                const loadButton = document.getElementById('open_dialog1');
                const selectContainer = document.getElementById('dynamic-select-container');
                const selectElement = document.getElementById('dynamic-select');

                let isChoicesInitialized = false;

                loadButton.addEventListener('click', () => {

                    let mmSelectId = clsTableMngJs.getSelectingCheckBox();
                    if (!mmSelectId.length) {
                        alert("Bạn chưa chọn danh sách user để thêm? Hãy chọn với check box!");
                        return;
                    }

                    $("#select_number_item").html(" <i class='fa fa-users'></i> Đã chọn: <b class='text-danger'>" + mmSelectId.length + " thành viên</b>, hãy chọn Sự kiện để thêm vào:");

                    // Hiển thị select container
                    selectContainer.style.display = 'block';

                    // Chỉ khởi tạo Choices.js một lần
                    if (!isChoicesInitialized) {
                        isChoicesInitialized = true;
                        const choices = new Choices(selectElement, {
                            shouldSort: false, // Không sắp xếp lại danh sách
                            searchEnabled: true,
                            removeItemButton: true,
                            placeholder: true, // Bật hỗ trợ placeholder
                            itemSelectText: 'Select',
                        });

                        // Fetch dữ liệu từ API và thêm vào select
                        // fetch('https://jsonplaceholder.typicode.com/users')
                        fetch(
                            '<?php
                            if(Helper1::isMemberModule())
                                echo '/api/member-event-info/list?soby_s1=desc&limit=100&_only_fields_=id,name';
                            else
                                echo '/api/event-info/list?soby_s1=desc&limit=100&_only_fields_=id,name';
                            ?>')
                                .then(response => response.json())
                            .then(data => {

                                console.log(" data = " , data);
                                // Kiểm tra cấu trúc dữ liệu trả về
                                if (data.code === 1 && data.payload?.data) {
                                    // Trích xuất và MAP dữ liệu
                                    const options = data.payload.data.map(item => ({
                                        value: item.id,
                                        label: "(" + item.id + `) ` + item.name,
                                    }));

                                    // Đưa vào Choices.js
                                    choices.setChoices(options, 'value', 'label', true);
                                } else {
                                    console.error('Invalid API response:', data);
                                }
                            })
                            .catch(error => {
                                console.error('Error fetching data:', error);
                            });
                    }
                });
            });
        </script>

        <?php

    }

    //...
}
