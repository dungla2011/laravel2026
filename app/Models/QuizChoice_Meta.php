<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class QuizChoice_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/quiz-choice';

    protected static $web_url_admin = '/admin/quiz-choice';

    protected static $api_url_member = '/api/member-quiz-choice';

    protected static $web_url_member = '/member/quiz-choice';

    //public static $folderParentClass = QuizChoiceFolderTbl::class;
    public static $modelClass = QuizChoice::class;

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();
        if ($field == 'status' || $field == 'is_right_choice') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //QuizChoice edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }
        if ($field == 'value_richtext') {
            $objMeta->dataType = DEF_DATA_TYPE_RICH_TEXT;
        }

        return $objMeta;
    }

    //...
}
