<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class HrTask_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/hr-task';

    protected static $web_url_admin = '/admin/hr-task';

    protected static $api_url_member = '/api/member-hr-task';

    protected static $web_url_member = '/member/hr-task';

    //public static $folderParentClass = HrTaskFolderTbl::class;
    public static $modelClass = HrTask::class;

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();
        if ($field == 'status' || $field == 'done') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //HrTask edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }
        if ($field == 'job_id' || $field == 'type') {
            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;
        }

        if ($field == 'user_id_get') {
            $objMeta->join_api_field = 'email';
            //            $objMeta->join_func = 'joinUserEmailUserId';
            $objMeta->join_api = '/api/hr-employee/search_user';
        }

        return $objMeta;
    }

    public function setDefaultValue($field)
    {
        if ($field == 'user_id') {
            return getUserIdCurrentInCookie();
        }
    }

    public function _user_id($obj, $val, $field)
    {
        return (new HrEmployee_Meta())->_user_id($obj, $val, $field);
    }

    public function _user_id_get($obj, $val, $field)
    {
        return (new HrEmployee_Meta())->_user_id($obj, $val, $field);
    }

    public function _type($obj, $val, $field)
    {
        $mm = [];
        $mm[0] = ' -- Chọn Thể loại --';
        $mm[1] = ' 1 Lần ';
        $mm[2] = ' Hàng ngày ';
        $mm[3] = ' Hàng Tuần ';
        $mm[4] = ' Hàng Tháng ';
        $mm[5] = ' Hàng Quý ';
        $mm[6] = ' Hàng Năm ';

        return $mm;
    }

    public function _job_id($obj, $val, $field)
    {
        $mm = [0 => '--Chọn Công Việc--'];
        $m1 = HrJob::where('status', '=', 1)->get();
        if ($m1) {
            foreach ($m1 as $j1) {
                $mm[$j1->id] = $j1->name;
            }
        }

        return $mm;
    }

    public function _name($obj)
    {
        return "<div data-id='$obj->id' class='' style='background-color: lavender; padding: 5px; text-align: right'> <i data-id='$obj->id' style='' class='fa-2x far fa-comment-dots open_chat_box'></i> </div>";
    }

    //...
    public function extraJsInclude()
    {
        HrMessageTask_Meta::loadTaskMessageDialog();
    }
}
