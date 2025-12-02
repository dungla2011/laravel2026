<?php

namespace App\Models;

use App\Components\Helper1;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Common\Database\MetaOfTableInDb;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Meta;

/**
 * ABC123
 * @param null $objData
 */
class DepartmentUser_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = "/api/department-user";
    protected static $web_url_admin = "/admin/department-user";

    protected static $api_url_member = "/api/member-department-user";
    protected static $web_url_member = "/member/department-user";

    //public static $folderParentClass = DepartmentUserFolderTbl::class;
    public static $modelClass = DepartmentUser::class;

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

//        if($field == 'status'){
//            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
//        }

        if($field == 'tag_list_id'){
            $objMeta->join_api_field = 'name';
//          $objMeta->join_func = 'joinTags';
            //DepartmentUser edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }
        if($field == 'parent_extra' || $field == 'parent_all' ){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_MULTI_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\DepartmentUserFolderTbl::joinFuncPathNameFullTree';
        }

        if($field == 'parent_id'){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\DepartmentUserFolderTbl::joinFuncPathNameFullTree';
        }

        if($field == 'department_id'){
            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;
        }



        //Nếu không set thì lấy của parent default nếu có
        if(!$objMeta->dataType)
            if($ret = parent::getHardCodeMetaObj($field))
                return $ret;

        return $objMeta;
    }
    function _image_list1($obj, $val, $field){
        return Helper1::imageShow1($obj, $val, $field);
    }

    function _department_id($obj, $val)
    {

        //Lấy ra all department, và đưa ra  mảng tất cả id=>name
        $departments = Department::all();
        $departmentArr = $departments->pluck('name', 'id')->toArray();
        //Đưa  0 -> chọn vào đầu mảng
        $departmentArr = [0 => '-- Chọn Đơn vị -- '] + $departmentArr;
        return $departmentArr;
    }

    public function getFullSearchJoinField()
    {
        return [
            'users.email' => "like",
            'users.name' => "like",
        ];
    }

    function getSqlOrJoinExtraIndex(\Illuminate\Database\Eloquent\Builder &$x = null, $getSelect = 0)
    {
        return $x->leftJoin('users', 'user_id', '=', 'users.id')
            ->addSelect([
                'users.email AS _email',
                'users.name as _name',
            ]);
    }

    function _email($obj, $val)
    {
        return " <a href='/admin/user-api/edit/$obj->user_id'> $val </a> ";
    }

    function _name($obj, $val)
    {
        return $val;
    }

    public function executeBeforeIndex($param = null)
    {
        //Tìm tất cả các user trong Users, chưa có trong bảng DepartmentUser và insert các userid vào bảng này

        //Chỉ lấy ra userid để giảm nhẹ query

        $users = User::all();
        $departmentUsers = DepartmentUser::all();
        $departmentUserIds = $departmentUsers->pluck('user_id')->toArray();
        $newUsers = $users->filter(function($user) use ($departmentUserIds){
            return !in_array($user->id, $departmentUserIds);
        });
        $newUsers->each(function($user){
            $departmentUser = new DepartmentUser();
            $departmentUser->user_id = $user->id;
            $departmentUser->save();
        });


    }

    //...




}
