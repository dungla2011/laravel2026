<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class MediaFolder_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/media-folder';

    protected static $web_url_admin = '/admin/media-folder';

    protected static $api_url_member = '/api/member-media-folder';

    protected static $web_url_member = '/member/media-folder';

    public static $folderParentClass = MediaFolder::class;

    public static $modelClass = MediaFolder::class;

    public static $allowAdminShowTree = 1;

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
            //MediaFolder edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    //...
}
