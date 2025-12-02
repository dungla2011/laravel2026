<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class UserGroup_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/user-group';

    protected static $web_url_admin = '/admin/user-group';

    protected static $api_url_member = '/api/member-user-group';

    protected static $web_url_member = '/member/user-group';

    //public static $folderParentClass = UserGroupFolderTbl::class;
    public static $modelClass = UserGroup::class;

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();
        if ($field == 'status') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //UserGroup edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    //...
}
