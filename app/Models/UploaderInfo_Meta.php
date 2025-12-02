<?php

namespace App\Models;

use App\Components\Helper1;
use App\Components\U4sHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Common\Database\MetaOfTableInDb;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Meta;

/**
 * ABC123
 * @param null $objData
 */
class UploaderInfo_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = "/api/uploader-info";
    protected static $web_url_admin = "/admin/uploader-info";

    protected static $api_url_member = "/api/member-uploader-info";
    protected static $web_url_member = "/member/uploader-info";

    //public static $folderParentClass = UploaderInfoFolderTbl::class;
    public static $modelClass = UploaderInfo::class;

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
            //UploaderInfo edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }
        if($field == 'parent_extra' || $field == 'parent_all' ){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_MULTI_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\UploaderInfoFolderTbl::joinFuncPathNameFullTree';
        }

        if($field == 'parent_id'){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\UploaderInfoFolderTbl::joinFuncPathNameFullTree';
        }

        //Nếu không set thì lấy của parent default nếu có
        if(!$objMeta->dataType)
            if($ret = parent::getHardCodeMetaObj($field))
                return $ret;

        if ($field == 'user_id') {
            $objMeta->join_api_field = 'email';
            //            $objMeta->join_func = 'joinUserEmailUserId';
            $objMeta->join_api = '/api/user/search';
        }

        return $objMeta;
    }

    public function extraContentIndex1($v1 = null, $v2 = null, $v3 = null)
    {

        $ret = "<div class='m-2'>
    <button class='btn btn-default btn-sm'>
    <a href='/tool/4s/list_uploader_big.php' target='_blank'> GET UPLODER BIG SIZE</a>
</button>
</div>";

        echo $ret;

    }

    function _image_list1($obj, $val, $field){
        return Helper1::imageShow1($obj, $val, $field);
    }

    //...
    public function _user_id($objData, $value = null, $field = null)
    {
        return  User_Meta::search_user_email($objData, $value, $field);
    }


}
