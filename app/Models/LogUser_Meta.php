<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class LogUser_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/log-user';

    protected static $web_url_admin = '/admin/log-user';

    protected static $api_url_member = '/api/member-log-user';

    protected static $web_url_member = '/member/log-user';

    //public static $folderParentClass = LogUserFolderTbl::class;
    public static $modelClass = LogUser::class;

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();
        if ($field == 'status') {
            //            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //LogUser edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    public function _url($obj, $val)
    {
        return "<a href='$val' target='_blank'>$val</a>";
    }

    public function _user_id($obj, $val, $field)
    {
        return (new HrEmployee_Meta())->_user_id($obj, $val, $field);
    }

    //...
}
