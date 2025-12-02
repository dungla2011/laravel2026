<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class DonViHanhChinh_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/don-vi-hanh-chinh';

    protected static $web_url_admin = '/admin/don-vi-hanh-chinh';

    protected static $api_url_member = '/api/member-don-vi-hanh-chinh';

    protected static $web_url_member = '/member/don-vi-hanh-chinh';

    public static $folderParentClass = DonViHanhChinh::class;

    public static $modelClass = DonViHanhChinh::class;

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();

        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //DonViHanhChinh edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    //...
}
