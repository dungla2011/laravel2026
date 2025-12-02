<?php

namespace App\Models;

use Illuminate\Support\Str;
use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class NewsFolder_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/news-folder';

    protected static $web_url_admin = '/admin/news-folder';

    protected static $api_url_member = '/api/member-news-folder';

    protected static $web_url_member = '/member/news-folder';

    public static $folderParentClass = NewsFolder::class;

    public static $modelClass = NewsFolder::class;

    public static $titleMeta = " Danh Mục tin tức";

    public function getPublicLink($objOrId)
    {

        if (is_object($objOrId)) {
            $obj = $objOrId;
        } else {
            if (! is_numeric($objOrId)) {
                $objOrId = qqgetIdFromRand_($objOrId);
            }
            $obj = NewsFolder::find($objOrId);
        }
        if (! $obj) {
            loi(" Not found folder id: $objOrId");
        }
        $slug = Str::slug($obj->name);

        $link = '/tin-tuc/s/'.$slug.'.'.$obj->id;

        //        echo "\n <hr>  <h3>  <a class='news1' href='$link'> $obj->name </h3>
        return $link;
    }

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();

        if ($field == 'status' || $field == 'front') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //NewsFolder edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    //...
}
