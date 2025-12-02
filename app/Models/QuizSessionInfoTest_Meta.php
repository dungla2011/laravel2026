<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class QuizSessionInfoTest_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/quiz-session-info-test';

    protected static $web_url_admin = '/admin/quiz-session-info-test';

    protected static $api_url_member = '/api/member-quiz-session-info-test';

    protected static $web_url_member = '/member/quiz-session-info-test';

    //public static $folderParentClass = QuizSessionInfoTestFolderTbl::class;
    public static $modelClass = QuizSessionInfoTest::class;

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
            //QuizSessionInfoTest edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    public function isDateTimeType($field)
    {
        if (in_array($field, ['close_answer_time', 'open_answer_time', 'start_time_do', 'end_time_do'])) {
            return true;
        }

        return false;
    }

    //...
}
