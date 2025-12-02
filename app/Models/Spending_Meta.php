<?php

namespace App\Models;

use App\Components\Helper1;
use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class Spending_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/spending';

    protected static $web_url_admin = '/admin/spending';

    protected static $api_url_member = '/api/member-spending';

    protected static $web_url_member = '/member/spending';

    //public static $folderParentClass = SpendingFolderTbl::class;
    public static $modelClass = Spending::class;

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();
        if ($field == 'status') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if ($field == 'note') {
            $objMeta->dataType = DEF_DATA_TYPE_TEXT_AREA;
        }
        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //Spending edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }
        if ($field == 'image_list') {
            $objMeta->dataType = DEF_DATA_TYPE_IS_MULTI_IMAGE_BROWSE;
        }

        return $objMeta;
    }

    public function _user_id($obj, $val)
    {
        $user = User::find($val);
        if ($user) {
            return " <div style='font-size: small; padding: 3px'> $user->email </div> ";
        }
    }

    public function _image_list($obj, $val, $field)
    {
        return Helper1::imageShow1($obj, $val, $field);
    }

    //...
}
