<?php

namespace App\Models;

use App\Components\Helper1;
use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class BlockUi_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/block-ui';

    protected static $web_url_admin = '/admin/block-ui';

    protected static $api_url_member = '/api/member-block-ui';

    protected static $web_url_member = '/member/block-ui';

    //public static $folderParentClass = BlockUiFolderTbl::class;
    public static $modelClass = BlockUi::class;

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();

        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //BlockUi edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        if ($field == 'content') {
            $objMeta->dataType = DEF_DATA_TYPE_RICH_TEXT;
        }

        if ($field == 'summary') {
            $objMeta->dataType = DEF_DATA_TYPE_RICH_TEXT;
        }
        if ($field == 'summary2') {
            $objMeta->dataType = DEF_DATA_TYPE_RICH_TEXT;
        }

        if ($field == 'status') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }

        if ($field == 'img_list') {
            $objMeta->dataType = DEF_DATA_TYPE_IS_MULTI_IMAGE_BROWSE;
        }

        if ($field == 'image_list') {
            $objMeta->dataType = DEF_DATA_TYPE_IS_MULTI_IMAGE_BROWSE;
        }

        if ($field == 'guide_admin' || $field == 'extra_info') {
            $objMeta->dataType = DEF_DATA_TYPE_TEXT_AREA;
        }

        return $objMeta;
    }

    public function _image_list($obj, $val, $field)
    {
        return Helper1::imageShow1($obj, $val, $field);
    }

    public function getHeightTinyMce($field)
    {
        if ($field == 'summary' || $field == 'summary2') {
            return 150;
        }
    }

    public function extraHtmlIncludeEdit0($v1 = null, $v2 = null, $v3 = null)
    {

        $link = "/tool/common/block_ui_multi_language.php#id={$v1->id}";

        echo "<div class='my-2 p-2 btn'> <a href='$link' target='_blank'> Edit Multi Language</a> </div> ";
    }

    //...
}
