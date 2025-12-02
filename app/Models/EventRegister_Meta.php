<?php

namespace App\Models;

use App\Components\Helper1;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Common\Database\MetaOfTableInDb;
use LadLib\Common\UrlHelper1;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Meta;

/**
 * ABC123
 * @param null $objData
 */
class EventRegister_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = "/api/event-register";
    protected static $web_url_admin = "/admin/event-register";

    protected static $api_url_member = "/api/member-event-register";
    protected static $web_url_member = "/member/event-register";

    public static $titleMeta = 'Danh sách đăng ký sự kiện';

    //public static $folderParentClass = EventRegisterFolderTbl::class;
    public static $modelClass = EventRegister::class;


    public function getJoinField111()
    {
        return [];
        $mm = [
            'registered' => [
                'table' => 'event_and_users',
                'field' => 'user_event_id',
                'field_local' => 'user_event_id',
                'field_remote' => 'user_event_id',
                'field_local1' => 'event_id',
                'field_remote1' => 'event_id',
            ],
        ];

        return $mm;
    }

    function _bank_name_text($obj, $val, $field)
    {

        $allowedBanks = config('banks');

        if(isAdminCookie()){
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            dump($allowedBanks);
//            echo "</pre>";
//            die();
        }

        $mm = [];
        $mm[0] = "-Chọn Bank-";

        foreach ($allowedBanks AS $key => $name) {
//            $name = explode('-',$name)[1] ?? '';
            $name = $name['public_name'] ?? '';
            $mm[$key] = "$key - $name";
        }
        ksort($mm);
        return $mm;
    }

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

        if($field == 'status'){
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }

        if ($field == 'image_list') {
            $objMeta->dataType = DEF_DATA_TYPE_IS_MULTI_IMAGE_BROWSE;
        }


        if($field == 'tag_list_id'){
            $objMeta->join_api_field = 'name';
//          $objMeta->join_func = 'joinTags';
            //EventRegister edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }
        if($field == 'parent_extra' || $field == 'parent_all' ){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_MULTI_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\EventRegisterFolderTbl::joinFuncPathNameFullTree';
        }

        if($field == 'content_mail1' || $field == 'content_mail2' ){
            $objMeta->dataType = DEF_DATA_TYPE_RICH_TEXT;
        }

        if($field == 'gender' || $field == 'bank_name_text'){
            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;
        }

        if($field == 'parent_id'){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\EventRegisterFolderTbl::joinFuncPathNameFullTree';
        }

        //Nếu không set thì lấy của parent default nếu có
        if(!$objMeta->dataType)
            if($ret = parent::getHardCodeMetaObj($field))
                return $ret;

        return $objMeta;
    }


    function _sub_event_list($obj, $val)
    {
       if($ev = EventInfo::find($obj->event_id)){
         $ret =  EventInfo::htmlSubEventInputCheck($ev, explode(',', $val));

         return     $ret = "<div class='p-3'> $ret </div>";
       }
    }

    function _reg_confirm_time($obj, $val)
    {

        //Nếu là sự kiện con thì không có send mail ở đây
        if($ev = EventInfo::find($obj->event_id)){
            if($ev->parent_id)
                return "(Sự kiện con)";
        }

        $str = " <div class='mx-2 mb-2'  style='font-size: 80%' data-code-pos='ppp17313121227581'>";
        if(!$val){
            $str .= " <button title='Nếu user chưa nhận được mail, có thể gửi lại'
 type='button' data-id='$obj->id'  class='re_send_email btn btn-sm btn-primary'
 style='display: block!important; font-size:80%; padding: 3px 5px'>
Gửi mail confirm</button>  ";
        }
        else{
            $str .= " $val ";
        }

        $str .= "</div>";

        return $str;
    }


    function getSqlOrJoinExtraIndex(\Illuminate\Database\Eloquent\Builder &$x = null, $getSelect = 0)
    {
        if(Helper1::isMemberModule()){
            $mEventId = EventInfo::getEventIdListInDeparmentOfUser(getCurrentUserId());
            $x->whereIn('event_registers.event_id',  $mEventId);
        }
    }

    public function executeBeforeIndex($param = null)
    {
        //Tìm các id của EventInfo được tạo bở userid này, sau đó
        $user_id = getCurrentUserId();
        $mmEv = EventInfo::where('user_id', $user_id)->get();
        foreach ($mmEv as $ev) {
            //ở EventAndUser, hãy SET user_id này cho mọi EventRegister có các event_id vừa tìm được, nếu userid khác
//            EventRegister::where('event_id', $ev->id)->where("user_id",'!=', $user_id)->update(['user_id' => $user_id]);
            EventRegister::where('event_id', $ev->id)->update(['user_id' => $user_id]);
        }
    }


    public function getFullSearchJoinField()
    {
        return [
            'first_name'  => "like",
            'last_name'  => "like",
            'email'  => "like",
            'phone'  => "like",
            'address'  => "like",
            'organization'  => "like",
            'designation'  => "like",
        ];
    }

    function _event_id($obj, $val, $field){
        if($ev = EventInfo::find($obj->event_id)){
            $tmp = \classStr::substr_fit_char_unicode($ev->name, 0,50,1);
            return "<div style='font-size: small; padding: 5px' title='$ev->name'> ". $tmp ." </div>";
        }
    }

    function _id($obj, $val, $field){

        $email = $obj->email;

        if($evu = EventUserInfo::where('email', $email)->first()){
            //Neu da dang ky thi bao da dang ky xong:
            if($evAndU = EventAndUser::where(['event_id' => $obj->event_id, 'user_event_id' => $evu->id])->first()){
                return "<div>
<button style='font-size:70%' type='button' data-id='$obj->id' data-confirm-mail='$obj->reg_confirm_time' class='approve_user btn btn-sm btn-default'>
Đã duyệt </button>
</div>";
            }
        }

        return "<div>
<button  style='font-size:70%' type='button' data-id='$obj->id' data-confirm-mail='$obj->reg_confirm_time'  class='approve_user btn btn-sm btn-primary'
title='Duyệt vào Sự kiện'> Duyệt </button>
</div>";

    }

    function _image_list($obj, $val, $field){
        return Helper1::imageShow1($obj, $val, $field);
    }

    function _gender($obj, $val, $field){
        return [
            0 => '--Chọn--',
            1 => 'Nam',
            2 => 'Nữ',
            3 => 'Khác',
        ];
    }

    public function extraCssIncludeEdit()
    {

        ?>

        <style>
            .container_sub_event_list {
                padding: 20px;
                background-color: snow;

                border: 1px solid #eee; border-radius: 5px; margin-top: 20px;
                color: black;
            }
            .sub_event_info {
                cursor: pointer;
                padding: 15px 15px;
                margin: 15px 0px 0px 0px;
                border: 1px solid #ddd;
                background-color: white;
                border-radius: 5px;
                color: black;
            }
            .sub_event_info:hover {
                background-color: #ddd;
            }
        </style>

        <?php
    }

    function extraJsIncludeEdit($objData = null)
    {

        ?>

        <script>
            //Onload
            window.addEventListener('load', function () {

                if(document.querySelector('input[name="sub_event_list"]'))
                    document.querySelector('input[name="sub_event_list"]').readOnly = true;

                //Click vào div thì check vào checkbox
                document.querySelectorAll('.sub_event_info').forEach(function (element) {
                    element.addEventListener('click', function (event) {

                        console.log("Click div sub_event_info");

                        if (event.target.tagName !== 'INPUT') {
                            const checkbox = this.querySelector('.check_sub_event');
                            checkbox.checked = !checkbox.checked;
                        }


                        let idList = "";
                        // //Lay ra all id duoc check, roi dua vao input[name=sub_event_list]
                        const subEventList = document.querySelectorAll('input.check_sub_event');
                        // let idList = '';
                        subEventList.forEach(function (element) {
                            if (element.checked) {
                                idList += element.id.replace('sub_event_', '') + ',';
                            }
                        });
                        idList = idList.replace(/^,|,$/g, ''); // Loại bỏ dấu ',' ở đầu hoặc cuối chuỗi
                        console.log("idList = ", idList);

                        if(document.querySelector('input[name="sub_event_list"]'))
                            document.querySelector('input[name="sub_event_list"]').value = idList;
                    });

                    const checkbox = element.querySelector('.check_sub_event');
                    if(checkbox)
                    checkbox.addEventListener('click', function (event) {
                        event.stopPropagation();
                    });
                });
            });

        </script>

        <?php

    }

    public function extraCssInclude()
    {
        ?>
        <style>
            .input_value_to_post.reg_confirm_time {
                display: none;
            }
        <?php
        if(request('seby_s11')){
            ?>
            .divTable2Cell.event_id, .divTable2Cell[data-table-field="event_id"]{
                display: none;
            }
            .divTable2Cell div.id_data{
                /*display: none;*/
            }
            div[data-table-field='id']{
                text-align: center;
            }


        <?php
    }

    ?>

            div[data-code-pos='ppp16881003038811'] {
                text-align: ;
            }

            div[data-table-field='id'] {
                /*width: 130px;*/
                height: 50px
            }
            div[data-table-field='id'] button{
                margin: 5px;
            }
        </style>

        <?php
    }


    public function getSqlOrJoinExtraEdit(\Illuminate\Database\Eloquent\Builder &$x = null, $params = null)
    {
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

        <script>

            window.addEventListener('load', function () {
                let user_token = jctool.getCookie('_tglx863516839');
                $(".re_send_email").on('click', function (){
                    let dataId = $(this).data('id');
                    console.log(" re_send_email " , dataId);
                    showWaittingIcon();
                    let url = "/api/event-info/sendRegConfirmMail?reg_id=" + dataId;
                    $.ajax({
                        url: url,
                        type: 'GET',
                        // data: {mmSelectId: mmSelectId},
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                        },
                        success: function (data, status) {
                            hideWaittingIcon();
                            console.log("Data ret: ", data, " \nStatus: ", status);
                            if (data.payload) {
                                console.log(" get all Data ", data.payload.data);
                                if(data.code == 1){
                                    showToastInfoTop(data.payload);
                                }
                                else
                                    alert(data.payload);
                                // $("#div_list_bai_test_id").html(JSON.stringify(data.payload.data));
                            }
                            else{
                                alert("Error approve!\n" + JSON.stringify(data).substring(0, 1000));
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
                })

                $(function () {


                    $(".approve_user").on('click', function () {
                        let id = $(this).data('id');
                        let confirm_mail = $(this).data('confirm-mail');

                        console.log("Add cmd ");

                        if(!confirm_mail){
                            if(!confirm("Thành viên Chưa xác nhận email: \nEmail gửi đến sau khi Thành viện đăng ký chưa được Click Link xác nhận!" +
                                "\n" +
                                "Nếu bạn đồng ý Duyệt thành viên, là chưa chắc chắn email này có tồn tại, hợp lệ không, hoặc không phải do User này đăng ký! \n" +
                                "Nếu Email không hợp lệ, việc gửi mail tự động sau này sẽ không thành công\n\n" +
                                "Bạn Có chắc chắn Duyệt hoặc Bỏ qua Không duyệt?"))
                            return;
                        }

                        let that = this;

                        // let mmSelectId = clsTableMngJs.getSelectingCheckBox();

                        showWaittingIcon();
                        let url = "/api/event-info/approvePublicUser?event_register_id=" + id;
                        $.ajax({
                            url: url,
                            type: 'GET',
                            // data: {mmSelectId: mmSelectId},
                            beforeSend: function (xhr) {
                                xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                            },
                            success: function (data, status) {
                                hideWaittingIcon();
                                console.log("Data ret: ", data, " \nStatus: ", status);
                                if (data.payload) {
                                    console.log(" get all Data ", data.payload.data);
                                    if(data.code == 1){
                                        showToastInfoTop(data.payload);
                                        $(that).removeClass('btn-primary').addClass('btn-default').html('Đã duyệt');
                                    }
                                    else
                                        alert(data.payload);
                                    // $("#div_list_bai_test_id").html(JSON.stringify(data.payload.data));
                                }
                                else{
                                    alert("Error approve!\n" + JSON.stringify(data).substring(0, 1000));
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
                    })


                    $("#open_dialog1").on('click', function () {


                        let mmSelectId = clsTableMngJs.getSelectingCheckBox();
                        console.log(" mmSelectId = ", mmSelectId );
                        if(!mmSelectId.length){
                            alert("Bạn chưa chọn danh sách? Hãy chọn với check box!");
                            return;
                        }


                        $("#dialog1").dialog('open');
                    })
                    $("#close_dialog1").on('click', function () {
                        $("#dialog1").dialog('close');
                    })

                    $("#dialog1").dialog({
                        resizable: true,
                        height: 600,
                        autoOpen: false,
                        width: 1000,
                        modal: true,
                        open: function (event, ui) {
                            console.log('opened ...')
                            showWaittingIcon();
                            let url = "/api/quiz-test/list";
                            $.ajax({
                                url: url,
                                type: 'GET',
                                beforeSend: function (xhr) {
                                    xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                                },
                                success: function (data, status) {
                                    hideWaittingIcon();
                                    console.log("Data ret: ", data, " \nStatus: ", status);
                                    if (data.payload) {
                                        console.log(" get all Data ", data.payload.data);


                                        // $("#div_list_bai_test_id").html(JSON.stringify(data.payload.data));
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


                        },
                    });

                });
            });

        </script>

        <?php

    }

}
