<?php

namespace App\Models;

use App\Components\Helper1;
use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class HrLogTask_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/hr-log-task';

    protected static $web_url_admin = '/admin/hr-log-task';

    protected static $api_url_member = '/api/member-hr-log-task';

    protected static $web_url_member = '/member/hr-log-task';

    //public static $folderParentClass = HrLogTaskFolderTbl::class;
    public static $modelClass = HrLogTask::class;

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
            //HrLogTask edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }
        if ($field == 'user_id') {
            $objMeta->join_api_field = 'email';
            //            $objMeta->join_func = 'joinUserEmailUserId';
            $objMeta->join_api = '/api/hr-employee/search_user';
        }

        return $objMeta;
    }

    public function _user_id($obj, $val, $field)
    {
        return (new HrEmployee_Meta())->_user_id($obj, $val, $field);
    }

    public function _task_id($obj, $val)
    {

        $task = HrTask::find($obj->task_id);

        return $task->name;

    }

    public function _image_list($obj, $val, $field)
    {
        return Helper1::imageShow1($obj, $val, $field);
    }

    //...
}
