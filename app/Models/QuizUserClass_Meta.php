<?php

namespace App\Models;

use App\Components\Helper1;
use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class QuizUserClass_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/quiz-user-class';

    protected static $web_url_admin = '/admin/quiz-user-class';

    protected static $api_url_member = '/api/member-quiz-user-class';

    protected static $web_url_member = '/member/quiz-user-class';

    public static $folderParentClass = QuizClass::class;

    public static $modelClass = QuizUserClass::class;

    public static $allowAdminShowTree = 1;

    //Join với 1 bảng khác
    public function getJoinField()
    {
        $mm = [
            'email1' => [
                'table' => 'users',
                'field' => 'email',
                'field_local' => 'user_id',
                'field_remote' => 'id',
            ],
        ];

        return $mm;
    }

    public function _user_id($obj)
    {
        $uid = $obj->user_id;
        if ($us = User::find($uid)) {
            return " <div style='font-size: small; margin: 5px'> $us->email </div>";
        }

        return 'abc';
    }

    public function extraCssInclude()
    {
        ?>
        <style>
            input.user_id {
                display: none
            }
        </style>
        <?php
    }

    public function _email1($obj)
    {
        $uid = $obj->user_id;
        if ($us = User::find($uid)) {
            return " <div style='font-size: small; margin: 5px'> $us->email </div>";
        }

    }

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();

        //Riêng Data type của Field, Lấy ra các field datatype mặc định
        //Nếu có thay đổi sẽ SET bên dưới
        $objSetDefault = new MetaOfTableInDb();
        $objSetDefault->setDefaultMetaTypeField($field);
        $objMeta->dataType = $objSetDefault->dataType;

        if ($field == 'parent_id') {
            $objMeta->dataType = DEF_DATA_TYPE_TREE_SELECT;
            $objMeta->join_api = '/api/quiz-class';
            //            $objMeta->join_func = 'App\Models\DemoFolderTbl::joinFuncPathNameFullTree';
        }
        if ($field == 'image_list') {
            $objMeta->dataType = DEF_DATA_TYPE_IS_MULTI_IMAGE_BROWSE;
        }

        return $objMeta;
    }

    //...

    public function extraContentIndex1($v1 = null, $v2 = null, $v3 = null)
    {
        $html = "<div class='mb-2'> <button id='addUserToClass' class='btn btn-sm btn-info'> ADD USER TO CLASS </button> </div> ";
        echo $html;

        $uid = getCurrentUserId();
        ?>
        <div id="dialog-select-test" title="Thêm user vào lớp" style="display: none; padding: 10px 20px">

            <p>Lớp:
                <!--                <input class="form-control" id="new_test_name" type="text">-->

                <select name="" id="select_class" class="form-control">
                    <option value=""> -- Select --</option>
                    <?php

                    if(Helper1::isMemberModule())
                        $mm = QuizClass::where('user_id', $uid)->orderBy('name', 'DESC')->get()->toArray();
                    else
                        $mm = QuizClass::orderBy('name', 'DESC')->get()->toArray();


        $mm = array_reverse($mm);
        foreach ($mm as $obj) {
            $obj = (object) $obj;
            echo "<option value='$obj->id'> $obj->name </option>";
        }

        ?>

                </select>
            </p>

             <p></p>
            Danh sách User email (cách nhau bằng dấu phẩy):
            <textarea class="form-control" style="width: 100%; min-height: 100px" id="user_list"></textarea>
            <p></p>
            <div style="float: right">
                <button class="btn btn-primary" id="add_to_test"> Thêm</button>
                <button class="btn btn-default" id="close_add"> Bỏ qua</button>
            </div>
        </div>

        <script>
            window.onload = function ()
            {

                $("#add_to_test").on('click', function (){

                    let select_class = $("#select_class").val();
                    let listUser = $("#user_list").val();

                    console.log(".....", select_class, listUser);
                    let user_token = jctool.getCookie('_tglx863516839');

                    let url = "/api/quiz-test/addUserToClass";
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {listUserId: listUser, select_class: select_class},
                        async: false,
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                        },
                        success: function (data, status) {
                            hideWaittingIcon();
                            console.log("Data ret: ", data, " \nStatus: ", status);

                            if (data.payload){
                                showToastInfoTop(data.payload);

                            }
                            else{
                                alert("Có lỗi: " + JSON.stringify(data))
                            }
                        },
                        error: function (data) {
                            hideWaittingIcon();
                            console.log(" DATAx " , data);
                            if(data.responseJSON && data.responseJSON.message)
                                alert('Error call api: ' + "\n" + data.responseJSON.message)
                            else
                                alert('Error call api: ' + "\n" + url + "\n\n" + JSON.stringify(data).substr(0,1000));
                        }
                    });
                    // $("#dialog-select-test").dialog("close");
                })

                $("#dialog-select-test").dialog({
                    resizable: true,
                    height: "auto",
                    autoOpen: false,
                    width: 600,
                    modal: true,
                    open: function (event, ui) {
                        console.log('opened ...')
                    },
                });

                $("#addUserToClass").on('click', function (){
                    $("#dialog-select-test").dialog("open");
                })

                $("#close_add").on('click', function (){
                    console.log(".....");
                    $("#dialog-select-test").dialog("close");
                })

                $("#add_test").on('click', function (){
                    console.log(".....");
                    $("#dialog-select-test").dialog("open");
                })
            }

        </script>

<?php
    }

    public function _parent_id($obj, $valIntOrStringInt, $field)
    {
        //return " $val , $obj->id , $obj->parent ";

        //        if($field == 'parent_multi' || $field == 'parent_multi2')
        return parent::_parent_id_template($obj, $valIntOrStringInt, $field); // TODO: Change the autogenerated stub

        /*
        if(!$valIntOrStringInt)
            return null;

        $cls = get_called_class();

        $folderCls = static::$folderParentClass;
        $objFolder = new $folderCls;

//        if($objFolder instanceof DemoFolderTbl);

        $ret = '';
        $retApi = [];
        //if(strstr($valIntOrStringInt, ','))
        if($valIntOrStringInt)
        {
            $valIntOrStringInt = trim(trim($valIntOrStringInt,','));
            $mVal = explode(",", $valIntOrStringInt);


            if($mm = $objFolder->whereIn("id", $mVal)->get()){
                foreach ($mm AS $obj) {
                    $mName = $obj->getFullPathParentObj(2);
                    $retApi[$obj->id] = $obj->name;
                    $retApi[$obj->id] = $name0 = implode("/", $mName);;
                    $ret .= "<span class='one_node_name' title='remove this: $obj->id' data-id='$obj->id' data-field='$field'> [x] $name0</span>";
                }
            }

        }

        if(Helper1::isApiCurrentRequest())
            return $retApi;
//        else
//            return "xxxxxx <span title='' class='all_node_name' data-field='$field'>$ret </span>";

        return $ret;
        */
    }
}
