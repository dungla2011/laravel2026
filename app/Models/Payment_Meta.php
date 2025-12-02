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
class Payment_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = "/api/payment";
    protected static $web_url_admin = "/admin/payment";

    protected static $api_url_member = "/api/member-payment";
    protected static $web_url_member = "/member/payment";

    //public static $folderParentClass = PaymentFolderTbl::class;
    public static $modelClass = Payment::class;

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

        if ($field == 'image_list' || $field == 'image_list2') {
            $objMeta->dataType = DEF_DATA_TYPE_IS_MULTI_IMAGE_BROWSE;
            //            $objMeta->join_func = 'App\Models\News_Meta::joinFuncImageId';
        }

        if ($field == 'user_id') {
            $objMeta->join_api_field = 'email';
            //            $objMeta->join_func = 'joinUserEmailUserId';
            $objMeta->join_api = '/api/user/search';
        }

            if($field == 'tag_list_id'){
            $objMeta->join_api_field = 'name';
//          $objMeta->join_func = 'joinTags';
            //Payment edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }
        if($field == 'parent_extra' || $field == 'parent_all' ){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_MULTI_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\PaymentFolderTbl::joinFuncPathNameFullTree';
        }

        if($field == 'parent_id'){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\PaymentFolderTbl::joinFuncPathNameFullTree';
        }

        //Nếu không set thì lấy của parent default nếu có
        if(!$objMeta->dataType)
            if($ret = parent::getHardCodeMetaObj($field))
                return $ret;

        return $objMeta;
    }


    public function isUseRandId()
    {
//        if(\App\Models\SiteMng::getDbName() == env('DB_DATABASE_GLX_TESTING'))
//            return 0;

        return 1;
    }

    public function _user_id($objData, $value = null, $field = null)
    {
        return User_Meta::search_user_email($objData, $value, $field);
    }

    function _image_list($obj, $val, $field){
        return Helper1::imageShow1($obj, $val, $field);
    }

    //...




}
