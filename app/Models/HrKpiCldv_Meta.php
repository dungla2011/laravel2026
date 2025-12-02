<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class HrKpiCldv_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/hr-kpi-cldv';

    protected static $web_url_admin = '/admin/hr-kpi-cldv';

    protected static $api_url_member = '/api/member-hr-kpi-cldv';

    protected static $web_url_member = '/member/hr-kpi-cldv';

    //public static $folderParentClass = HrKpiCldvFolderTbl::class;
    public static $modelClass = HrKpiCldv::class;

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
            //HrKpiCldv edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    //...
}
