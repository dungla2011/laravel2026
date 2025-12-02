<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class HrSalaryMonthUser_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/hr-salary-month-user';

    protected static $web_url_admin = '/admin/hr-salary-month-user';

    protected static $api_url_member = '/api/member-hr-salary-month-user';

    protected static $web_url_member = '/member/hr-salary-month-user';

    //public static $folderParentClass = HrSalaryMonthUserFolderTbl::class;
    public static $modelClass = HrSalaryMonthUser::class;

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();
        if ($field == 'status') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if ($field == 'month') {
            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;

        }
        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //HrSalaryMonthUser edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    public function _month($obj, $val, $field)
    {
        return 'empty func!';
    }
}
