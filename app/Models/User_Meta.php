<?php

namespace App\Models;

use App\Components\Helper1;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use LadLib\Common\Database\MetaOfTableInDb;

class User_Meta extends MetaOfTableInDb
{
    //public $api_url = "http://localhost:9081/api/user";
    protected static $api_url_admin = '/api/user';

    protected static $web_url_admin = '/admin/user-api';

    public static $titleMeta = "Danh sách thành viên";

    public static $modelClass = User::class;

    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();
        if ($field == 'password') {
            $objMeta->dataType = DEF_DATA_TYPE_PASSWORD;
        }

        if ($field == 'is_admin') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }

        if ($field == 'language') {
            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;
        }

        if ($field == 'avatar') {
            $objMeta->dataType = DEF_DATA_TYPE_IS_MULTI_IMAGE_BROWSE;
        }

        if ($field == '_roles') {
//            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;

            $objMeta->join_func_model = '_roles';
        }

        return $objMeta;
    }

    function getMapJoinFieldAlias()
    {
        return [
            '_roles'=>'role_user.role_id',
        ];
    }

    function _language($obj, $val, $field)
    {
        $mm = [
            '' => '- Ngôn ngữ -',
        ];
        $mm = array_merge($mm, \clang1::getLanguageList());
//        if (isset($mm[$val])) {
//            return $mm[$val];
//        }
        return $mm;
    }

    public function getFullSearchJoinField()
    {
        return [
            'users.email'  => "like",
            'users.name'  => "like",
            'users.username'  => "like",
        ];
    }

    static function search_user_email($objData, $value = null, $field = null)
    {
        if (!$objData) {
            return null;
        }

        $ret = '';
        if ($value && $obj = \App\Models\User::where('id', $value)->first()) {
            $ret = "<span data-code-pos='ppp166502584' data-autocomplete-id='$objData->id-$field' class='span_auto_complete'
data-item-value='$obj->id' title='Remove this item'>$obj->email [x]</span>";
            $obj = json_decode($obj);
            //return 'abc';
            if (Helper1::isApiCurrentRequest()) {
                return [$obj->id => $obj->email];
            }

            return $ret;
        }

        return null;
    }

    public function afterInsertApi($objOrId, $get = null, $post = null)
    {
        if (is_numeric($objOrId)) {
            $objOrId = User::find($objOrId);
        }
        //Tạo API key
        $objOrId->email_active_at = nowyh();
//        $objOrId->token_user = eth1b('uid.'.$objOrId->id.'.'.microtime(1));
        $objOrId->log = nowyh().'#active by afterInsertDb';
        $objOrId->update();
    }

    function getSqlOrJoinExtraIndex(\Illuminate\Database\Eloquent\Builder &$x = null, $getSelect = 0)
    {

        return;
        $arrayFieldGetIndex = $this->getShowIndexAllowFieldList(1);
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($arrayFieldGetIndex);
//        echo "</pre>";

        //lấy ra mảng value, implode vào chuỗi để hiển thị ra
        //Implode vào chuỗi, với users. ở đầu
        $str = "";
        foreach ($arrayFieldGetIndex as $field) {
            if($field[0] != '_'){
                $str .= "users." . $field.',';
            }
        }
        $str = trim($str, ',');
        echo "\n$str";


        return;




            return $x->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
            ->leftJoin('roles', 'role_user.role_id', '=', 'roles.id')
            ->addSelect([
//                'roles.name AS _roles',
                DB::raw('GROUP_CONCAT(roles.name SEPARATOR ", ") AS _roles') // Dùng GROUP_CONCAT
            ])    ->groupBy($str) // Nhóm theo ID người dùng
                ;
            //DB::raw('GROUP_CONCAT(roles.name SEPARATOR ", ") AS _roles') // Dùng GROUP_CONCAT
    }

    public function isPassword($field)
    {
        if ($field == 'password') {
            return 1;
        }

        //DEF_DATA_TYPE_PASSWORD
        return 0;
    }

    public function _avatar($obj, $val, $field)
    {
        return Helper1::imageShow1($obj, $val, $field);
    }

    public function isDateTimeType($field)
    {
        if ($field == 'email_active_at') {
            return 1;
        }

        return 0;
    }

    //Bắt buộc phải có cùng tên model User->_roles
    //thì sẽ hoạt động ok
    public function _roles($obj, $val, $field)
    {

        if(!($obj instanceof User)){
//
//            return [];
//            //Lấy ra mảng id=>name của role
//            $roles = Role::all();
//            $roleArr = $roles->pluck('name', 'id')->toArray();
//            return $roleArr;
        }

        $obj->refresh();
//        $roleReg = ($obj->_roles) ;
//        dump($roleReg);
//        return;
        $roleReg = $obj->_roles;
        $ret1 = '';
        $selected = [];
        foreach ($roleReg as $r1) {
            $ret1 .= "$r1->id,";
            $selected[] = $r1->id;
        }

        $isIndex = null;
        $act = Route::getCurrentRoute()->getActionMethod();
        if (Route::getCurrentRoute()->getActionMethod() == 'index') {
            $isIndex = 1;
        }

        $mm = Role::all();
        $ret = "<div style='padding: 10px 20px'>";
        foreach ($mm as $role) {
            $pad = '';
            if (in_array($role->id, $selected)) {
                if ($isIndex) {
                    $ret .= " $role->name, ";

                    continue;
                }
                $pad = 'checked';
            }
            if (! $isIndex) {
                $ret .= " <input data-id='$obj->id' data-role-id='$role->id' class='check_roles' style='' $pad type='checkbox' id='input_role_$role->id' value='$role->id'>
  <label style='font-weight: normal' for='input_role_$role->id'> $role->name  </label>  &nbsp;";
            }
        }
        $ret .= '</div>';
        $ret1 = trim($ret1, ',');

//        return $ret1;

        return ['value_post' => $ret1, 'value_show' => $ret];
        //        return " $ret . ($obj->id) / $ret1";
    }


    public function extraContentIndex1($v1 = null, $v2 = null, $v3 = null)
    {
        ?>
        <div style="border: 1px dashed #ccc; padding: 10px; margin-bottom: 10px">
            <button class="btn-sm btn-default  mr-2" id="get_uid_list"> Lấy danh sách User ID </button>
            <button class="btn-sm btn-default mr-2" id="get_email_list"> Lấy danh sách Email </button>
            <a href="/admin/role-user?seby_s3=1" target="_blank">
            <button class="btn-sm btn-default mr-2" > Các user quyền Admin </button>
            </a>
            <a href="/admin/role-user?seby_s3=2"  target="_blank">
                <button class="btn-sm btn-default mr-2" > Các user quyền Manager </button>
            </a>
        </div>

        <?php
    }

    public function extraJsInclude()
    {
        ?>
        <script>
            $(function () {
                $("#get_uid_list, #get_email_list").on('click', function (){
                    console.log("xx1");
                    let strUid = '';
                    let strEmail = '';
                    let totalSelect = 0;
                    $(".select_one_check").each(function (){
                        if($(this).is(":checked")) {
                            let dtid = $(this).attr('data-id')
                            console.log(" ID = ", dtid);
                            if(dtid) {
                                totalSelect++;
                                $("input[data-id=" + $(this).attr('data-id') + "][data-field=email]").each(function () {
                                    strUid += dtid + ','
                                    console.log(" Found ", $(this).val());
                                    strEmail += $(this).val() + ','
                                })
                            }
                        }
                    })

                    if(!totalSelect) {
                        alert("Hãy chọn Check box Thành viên bên dưới muốn thực hiện");
                        return;
                    }

                    if(this.id == 'get_uid_list')
                        navigator.clipboard.writeText(strUid);
                    else
                        navigator.clipboard.writeText(strEmail);

                    showToastInfoTop(" Đã copy vào clipboard danh sách uid/email: " + totalSelect)

                    console.log(" strUid ", strUid);
                    console.log(" strEmail ", strEmail);
                })
            })
        </script>

        <?php

    }

    public function extraJsIncludeEdit($objData = null)
    {
        ?>

        <script>
            $(function (){

                $(".divTable2Row input[data-field=token_user]").hover(
                    function (){
                        $(this).css({"width": '100%' , "color" : 'gray'});
                    },
                    function (){
                        $(this).css({"width": '50px'});
                    }
                );

                $(document).on('change', ".check_roles", function (){
                    console.log("... Change ... roleid: ", $(this).attr('data-role-id'));
                    let dataId =  $(this).attr('data-id')
                    $(".input_value_to_post[data-field='_roles']").val('');
                    $(".input_value_to_post[data-field='_roles']").attr('');
                    $(".input_value_to_post[data-field='_roles']").prop('');
                    let newVal = '';
                    $('input.check_roles[data-id='+ dataId +']').each(function (){
                        if($(this).prop("checked"))
                            newVal += $(this).attr('data-role-id') + ',';
                    })
                    $(".input_value_to_post[data-field='_roles']").val(newVal);
                    $(".input_value_to_post[data-field='_roles']").attr(newVal);
                    $(".input_value_to_post[data-field='_roles']").prop(newVal);
                    console.log(" NewVal1 = ", newVal);
                })


            })

        </script>
        <?php
    }
}
