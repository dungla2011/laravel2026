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
class PayMoneylog_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = "/api/pay-moneylog";
    protected static $web_url_admin = "/admin/pay-moneylog";

    protected static $api_url_member = "/api/member-pay-moneylog";
    protected static $web_url_member = "/member/pay-moneylog";

    public static $titleMeta = "Đã thanh toán";
    //public static $folderParentClass = PayMoneylogFolderTbl::class;
    public static $modelClass = PayMoneylog::class;

    /**
     * @param $field
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field){
        $objMeta = new MetaOfTableInDb();
        if($field == 'status'){
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if($field == 'tag_list_id'){
            $objMeta->join_api_field = 'name';
//          $objMeta->join_func = 'joinTags';
            //PayMoneylog edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }
        if($field == 'parent_extra' || $field == 'parent_all' ){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_MULTI_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\PayMoneylogFolderTbl::joinFuncPathNameFullTree';
        }

        if($field == 'parent_id'){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\PayMoneylogFolderTbl::joinFuncPathNameFullTree';
        }

        //Nếu không set thì lấy của parent default nếu có
        if(!$objMeta->dataType)
            if($ret = parent::getHardCodeMetaObj($field))
                return $ret;

        return $objMeta;
    }
    function _image_list($obj, $val, $field){
        return Helper1::imageShow1($obj, $val, $field);
    }


    public function extraCssInclude()
    {
        ?>

        <style>
            input.input_value_to_post.image_list{
                display: none;
            }

        </style>
<?php
    }

    //...

}
