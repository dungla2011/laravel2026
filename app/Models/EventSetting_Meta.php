<?php

namespace App\Models;

use App\Components\Helper1;
use Elasticsearch\Endpoints\Cat\Help;
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
class EventSetting_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = "/api/event-setting";
    protected static $web_url_admin = "/admin/event-setting";

    protected static $api_url_member = "/api/member-event-setting";
    protected static $web_url_member = "/member/event-setting";

    //public static $folderParentClass = EventSettingFolderTbl::class;
    public static $modelClass = EventSetting::class;

    public static $titleMeta = "Sự kiện - Cài đặt tham số chung của Đơn vị/Phòng ban";

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
        if($field == 'tag_list_id'){
            $objMeta->join_api_field = 'name';
//          $objMeta->join_func = 'joinTags';
            //EventSetting edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }
        if($field == 'parent_extra' || $field == 'parent_all' ){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_MULTI_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\EventSettingFolderTbl::joinFuncPathNameFullTree';
        }
        if($field == 'value') {
            $objMeta->dataType = DEF_DATA_TYPE_TEXT_AREA;
        }

        if($field == 'parent_id'){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\EventSettingFolderTbl::joinFuncPathNameFullTree';
        }

        //Nếu không set thì lấy của parent default nếu có
        if(!$objMeta->dataType)
            if($ret = parent::getHardCodeMetaObj($field))
                return $ret;

        return $objMeta;
    }

    function _user_id($obj, $value)
    {
        $user = User::find($value);
        if($user)
            return " <div style='font-size: small; padding: 2px 10px'> $user->email </div> ";
    }

    function _department_id($obj, $value)
    {
        $objx = Department::find($value);
        if($objx)
            return " <div style='font-size: small; padding: 2px 10px'> $objx->name </div> ";
    }

    function getSqlOrJoinExtraIndex(\Illuminate\Database\Eloquent\Builder &$x = null, $getSelect = 0)
    {
        //sắp xếp theo thứ tự userid, và name
//        $x->orderBy('user_id')->orderBy('name');

        if (Helper1::isMemberModule()) {
            $id = EventInfo::getDepartmentIdOfUser(getCurrentUserId());
            $x->where('department_id', $id);
        }
    }

    public function extraContentIndex1($v1 = null, $v2 = null, $v3 = null)
    {
        //Tìm các userid có roleid = 2 từ bảng RoleUser
        $departmentIds = Department::pluck('id')->toArray();
        if(Helper1::isMemberModule()){
            return;
        }
        $linkOpt = UrlHelper1::getUriWithoutParam();
        $key = $this->getSNameFromField('department_id');
        echo "<select onchange='location.href=this.value || \"$linkOpt\"' class='form-control form-control-sm bg-light mb-3'>";
        echo "\n <option value=''> -- Tất cả Manager (Role Manager, có quyền Quản trị Sự kiện của từng Đơn vị) -- </option>";
        foreach ($departmentIds as $idx) {
            if(!$objU = Department::find($idx))
                continue;
            if(request()->input('seby_'.$key) == $objU->id)
                echo "\n <option value='$linkOpt?seby_$key=$objU->id' selected> ($objU->id) $objU->name </option>";
            else
                echo "\n <option value='$linkOpt?seby_$key=$objU->id'> ($objU->id) $objU->name </option>";
        }
        echo "\n </select>";

        return;

        //Tìm các userid có roleid = 2 từ bảng RoleUser
        $userIds = RoleUser::where('role_id', 2)->pluck('user_id')->toArray();

        if(Helper1::isMemberModule()){
            return;
        }

        $linkOpt = UrlHelper1::getUriWithoutParam();

        echo "<select onchange='location.href=this.value || \"$linkOpt\"' class='form-control form-control-sm bg-light mb-3'>";
        echo "\n <option value=''> -- Tất cả Manager (Role Manager, có quyền Quản trị Sự kiện của từng Đơn vị) -- </option>";
        foreach ($userIds as $userId) {
            if(!$objU = User::find($userId))
                continue;
            if(request()->input('seby_s5') == $objU->id)
                echo "\n <option value='$linkOpt?seby_s5=$objU->id' selected> ($objU->id) $objU->email </option>";
            else
                echo "\n <option value='$linkOpt?seby_s5=$objU->id'> ($objU->id) $objU->email </option>";
        }
        echo "\n </select>";

    }

    public function executeBeforeIndex($param = null)
    {

        //mảng các giá trị sau sẽ được insert vào kèm userid của người tạo, nếu chưa có
        //Nghĩa là name, userid sẽ là key
        $arrayInsert = [
//            'sms_channel' => 'Kênh SMS tương ứng với App Gửi SMS',
            'email_address_to_send' => 'Địa chỉ Email đại diện để gửi mail cho user',
            'email_user_name' => 'Tên Email đại diện để gửi mail cho user',
        ];

        $departmentIds = Department::pluck('id')->toArray();


        foreach ($departmentIds as $idx) {

            foreach ($arrayInsert as $name => $comment) {
                $obj = EventSetting::where('name', $name)->where('department_id', $idx)->first();
                if (!$obj) {
                    $obj = new EventSetting();
                    $obj->name = $name;
                    $obj->comment = $comment;
                    $obj->department_id = $idx;
                    $obj->save();
                } else {
                    if ($obj->comment != $comment) {
                        $obj->comment = $comment;
                        $obj->save();
                    }
                }
            }
        }

        return;
        //Tim cac user co role MNG, insert vao
        $userIds = RoleUser::where('role_id', 2)->pluck('user_id')->toArray();



//        $uid = getCurrentUserId();

        foreach ($userIds as $uid) {


            //Nếu uid này có role_id = 2 thì tiếp tục
            $user = User::find($uid);
            if (!$user)
                continue;
            if (!$user->hasRoleId(2))
                continue;

            foreach ($arrayInsert as $name => $comment) {
                $obj = EventSetting::where('name', $name)->where('user_id', $uid)->first();
                if (!$obj) {
                    $obj = new EventSetting();
                    $obj->name = $name;
                    $obj->comment = $comment;
                    $obj->user_id = $uid;
                    $obj->save();
                } else {
                    if ($obj->comment != $comment) {
                        $obj->comment = $comment;
                        $obj->save();
                    }
                }
            }
        }

    }

    function _image_list1($obj, $val, $field){
        return Helper1::imageShow1($obj, $val, $field);
    }

    //...




}
