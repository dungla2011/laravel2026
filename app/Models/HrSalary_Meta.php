<?php

namespace App\Models;

use LadLib\Common\cstring2;
use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class HrSalary_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/hr-salary';

    protected static $web_url_admin = '/admin/hr-salary';

    protected static $api_url_member = '/api/member-hr-salary';

    protected static $web_url_member = '/member/hr-salary';

    //public static $folderParentClass = HrSalaryFolderTbl::class;
    public static $modelClass = HrSalary::class;

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();
        if ($field == 'status') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if ($field == 'job_title_id') {
            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;
        }

        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //HrSalary edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    public function _salary_hour($obj, $val, $field)
    {
        return cstring2::toTienVietNamString3($val);
    }

    public function _salary_day($obj, $val, $field)
    {
        return cstring2::toTienVietNamString3($val);
    }

    public function _salary_week($obj, $val, $field)
    {
        return cstring2::toTienVietNamString3($val);
    }

    public function _salary_month($obj, $val, $field)
    {
        return cstring2::toTienVietNamString3($val);
    }

    public function _job_title_id($obj, $val, $field)
    {

        $mm = HrJobTitle::where('status', '>', 0)->get();
        $ret = [0 => '-Chọn-'];
        foreach ($mm as $o) {
            $ret[$o->id] = $o->name;
        }

        return $ret;
    }

    //...
}
