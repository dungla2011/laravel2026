<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class HatecoCertificate_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/hateco-certificate';

    protected static $web_url_admin = '/admin/hateco-certificate';

    protected static $api_url_member = '/api/member-hateco-certificate';

    protected static $web_url_member = '/member/hateco-certificate';

    //public static $folderParentClass = HatecoCertificateFolderTbl::class;
    public static $modelClass = HatecoCertificate::class;

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
            //HatecoCertificate edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    //...
}
