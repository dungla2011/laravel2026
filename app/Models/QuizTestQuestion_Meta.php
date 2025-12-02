<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class QuizTestQuestion_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/quiz-test-question';

    protected static $web_url_admin = '/admin/quiz-test-question';

    protected static $api_url_member = '/api/member-quiz-test-question';

    protected static $web_url_member = '/member/quiz-test-question';

    //public static $folderParentClass = QuizTestQuestionFolderTbl::class;
    public static $modelClass = QuizTestQuestion::class;

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();
        if ($field == 'status' || $field == 'enable') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //QuizTestQuestion edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    public function _test_id($obj)
    {
        $q = QuizTest::find($obj->test_id);
        if ($q) {
            return "<div style='font-size: small'> <a href='/admin/quiz-test/edit/$q->id' target='_blank'>Bài test: $q->name </a> </div>";
        }
    }

    public function _question_id($obj)
    {
        $q = QuizQuestion::find($obj->question_id);
        if ($q) {
            return "<div style='font-size: small'> <a href='/admin/quiz-question/edit/$q->id' target='_blank'>Câu hỏi: $q->name </a> </div>";
        }
    }

    //...
}
