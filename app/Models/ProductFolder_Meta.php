<?php

namespace App\Models;

use App\Components\Helper1;
use Illuminate\Support\Str;
use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class ProductFolder_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/product-folder';

    protected static $web_url_admin = '/admin/product-folder';

    protected static $api_url_member = '/api/member-product-folder';

    protected static $web_url_member = '/member/product-folder';

    public static $folderParentClass = ProductFolder::class;

    public static $modelClass = ProductFolder::class;

    public static $titleMeta = 'Danh mục Phân loại sản phẩm';

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();
        if ($field == 'status' || $field == 'front') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if ($field == 'content') {
            $objMeta->dataType = DEF_DATA_TYPE_RICH_TEXT;
        }
        if ($field == 'summary') {
            $objMeta->dataType = DEF_DATA_TYPE_TEXT_AREA;
        }
        if ($field == 'image_list') {
            $objMeta->dataType = DEF_DATA_TYPE_IS_MULTI_IMAGE_BROWSE;
        }

        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //ProductFolder edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }


    public function getApiUrl($module = 'admin', $widthDomain = 1)
    {
        return "/api/product-folder";
    }

    public function getPublicLink($objOrId)
    {

        if (is_object($objOrId)) {
            $obj = $objOrId;
        } else {
            if (! is_numeric($objOrId)) {
                $objOrId = qqgetIdFromRand_($objOrId);
            }
            $obj = ProductFolder::find($objOrId);
        }

        $slug = Str::slug($obj->name);
        $link = '/san-pham/danh-muc/'.$slug.'.'.$obj->id.'.html';

        //        echo "\n <hr>  <h3>  <a class='news1' href='$link'> $obj->name </h3>
        return $link;
    }

    public function _image_list($obj, $val, $field)
    {
        return Helper1::imageShow1($obj, $val, $field);
    }

    //...
}
