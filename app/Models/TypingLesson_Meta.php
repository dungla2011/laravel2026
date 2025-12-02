<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class TypingLesson_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/typing-lesson';

    protected static $web_url_admin = '/admin/typing-lesson';

    protected static $api_url_member = '/api/member-typing-lesson';

    protected static $web_url_member = '/member/typing-lesson';

    //public static $folderParentClass = TypingLessonFolderTbl::class;
    public static $modelClass = TypingLesson::class;

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
            //TypingLesson edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    //...
}
