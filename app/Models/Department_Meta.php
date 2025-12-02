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
class Department_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = "/api/department";
    protected static $web_url_admin = "/admin/department";

    protected static $api_url_member = "/api/member-department";
    protected static $web_url_member = "/member/department";

    //public static $folderParentClass = DepartmentFolderTbl::class;
    public static $titleMeta = 'Danh sách Phòng Ban';
    public static $modelClass = Department::class;

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
            //Department edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }
        if($field == 'parent_extra' || $field == 'parent_all' ){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_MULTI_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\DepartmentFolderTbl::joinFuncPathNameFullTree';
        }



        if($field == 'parent_id'){

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

    //...

    public function extraContentIndexButton1($v1 = null, $v2 = null, $v3 = null)
    {
        ?>
        <a href="/admin/department-user">
        <button class="btn btn-primary btn-sm float-right mt-2 ml-2" type="button"> Gán Thành viên </button>
        </a>
<!--        <a href="/admin/department-event">-->
<!--            <button class="btn btn-outline-primary btn-sm float-right mt-2 ml-2" type="button"> Sự kiện </button>-->
<!--        </a>-->
        <?php
    }

}
