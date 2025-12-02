<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class PartnerInfo_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/partner-info';

    protected static $web_url_admin = '/admin/partner-info';

    protected static $api_url_member = '/api/member-partner-info';

    protected static $web_url_member = '/member/partner-info';

    //public static $folderParentClass = PartnerInfoFolderTbl::class;
    public static $modelClass = PartnerInfo::class;

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
            //PartnerInfo edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    //...
}
