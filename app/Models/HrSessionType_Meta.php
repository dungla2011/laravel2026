<?php

namespace App\Models;

use App\Components\Helper1;
use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class HrSessionType_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/hr-session-type';

    protected static $web_url_admin = '/admin/hr-session-type';

    protected static $api_url_member = '/api/member-hr-session-type';

    protected static $web_url_member = '/member/hr-session-type';

    //public static $folderParentClass = HrSessionTypeFolderTbl::class;
    public static $modelClass = HrSessionType::class;

//    public static $enableAddMultiItem = 20;

    public static function enableAddMultiItem()
    {
        if(Helper1::isAdminModule())
            return 20;
        return 0;
    }
    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();
        if ($field == 'status') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if ($field == 'num1') {
            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;
        }
        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //HrSessionType edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    public function _num1($obj, $val, $field)
    {
        $mm = [0 => '-Chọn-', 1 => 'Lễ'];

        return $mm;
    }

    //...
}
