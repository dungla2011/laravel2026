<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class OcrImage_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/ocr-image';

    protected static $web_url_admin = '/admin/ocr-image';

    protected static $api_url_member = '/api/member-ocr-image';

    protected static $web_url_member = '/member/ocr-image';

    //public static $folderParentClass = OcrImageFolderTbl::class;
    public static $modelClass = OcrImage::class;

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
            //QuizQuestion edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        if ($field == 'image_list') {
            $objMeta->dataType = DEF_DATA_TYPE_IS_MULTI_IMAGE_BROWSE;
        }

        if ($field == 'content') {
            $objMeta->dataType = DEF_DATA_TYPE_RICH_TEXT;
        }

        if ($field == 'summary' || $field == 'draft') {
            $objMeta->dataType = DEF_DATA_TYPE_TEXT_AREA;
        }

        return $objMeta;
    }

    public function _image_list($obj, $val, $field)
    {
        $meta = new DemoTbl_Meta();

        return $meta->_image_list2($obj, $val, $field);
    }

    public function extraJsInclude()
    {
        echo '
        <style>
        .img_zone img {
        width: 300px!important;
        height: 500px!important;
        max-width: 1000px!important;
        max-height: 1000px!important;
        }

</style>
        ';
    }

    //...
}
