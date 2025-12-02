<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class MediaLink_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/media-link';

    protected static $web_url_admin = '/admin/media-link';

    protected static $api_url_member = '/api/member-media-link';

    protected static $web_url_member = '/member/media-link';

    //public static $folderParentClass = MediaLinkFolderTbl::class;
    public static $modelClass = MediaLink::class;

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
            //MediaLink edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    //...
}
