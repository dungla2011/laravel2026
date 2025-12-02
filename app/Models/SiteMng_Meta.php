<?php

namespace App\Models;

use App\Components\Helper1;
use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class SiteMng_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/site-mng';

    protected static $web_url_admin = '/admin/site-mng';

    protected static $api_url_member = '/api/member-site-mng';

    protected static $web_url_member = '/member/site-mng';

    //public static $folderParentClass = SiteMngFolderTbl::class;
    public static $modelClass = SiteMng::class;


    public static $disableAddItem = 1;

    //    public $ignoreIndexTable = 1;

    public function extraContentIndex1($v1 = null, $v2 = null, $v3 = null)
    {
        $siteID = SiteMng::getSiteId();
        echo "<div class='col-md-12'> SiteID: $siteID </div>";
    }

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();
        if ($field == 'status') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }

        if (str_starts_with($field,  'color')) {
            $objMeta->dataType = DEF_DATA_TYPE_IS_COLOR_PICKER;
        }

        if ($field == 'logo_image') {
            $objMeta->dataType = DEF_DATA_TYPE_IS_MULTI_IMAGE_BROWSE;
        }
        if ($field == 'logo_image2') {
            $objMeta->dataType = DEF_DATA_TYPE_IS_MULTI_IMAGE_BROWSE;
        }
        if ($field == 'logo_image3') {
            $objMeta->dataType = DEF_DATA_TYPE_IS_MULTI_IMAGE_BROWSE;
        }
        if ($field == 'og_image_default') {
            $objMeta->dataType = DEF_DATA_TYPE_IS_MULTI_IMAGE_BROWSE;
        }

        if ($field == 'google_analytics_code') {
            //            $objMeta->dataType = DEF_DATA_TYPE_TEXT_AREA;
        }
        if ($field == 'google_analytics_code2') {
            //            $objMeta->dataType = DEF_DATA_TYPE_TEXT_AREA;
        }
        if ($field == 'remarketting') {
            $objMeta->dataType = DEF_DATA_TYPE_TEXT_AREA;
        }
        if ($field == 'maintain_text') {
            $objMeta->dataType = DEF_DATA_TYPE_RICH_TEXT;
        }
        if ($field == 'livechat') {
            $objMeta->dataType = DEF_DATA_TYPE_TEXT_AREA;
        }
        if ($field == 'metaHeader') {
            $objMeta->dataType = DEF_DATA_TYPE_TEXT_AREA;
        }

        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //SiteMng edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    public function _logo_image($obj, $val, $field)
    {
        return Helper1::imageShow1($obj, $val, $field);
    }

    //logo_image logo_image2 logo_image3 og_image_default
    public function _logo_image2($obj, $val, $field)
    {
        return Helper1::imageShow1($obj, $val, $field);
    }

    public function _logo_image3($obj, $val, $field)
    {
        return Helper1::imageShow1($obj, $val, $field);
    }

    public function _og_image_default($obj, $val, $field)
    {
        return Helper1::imageShow1($obj, $val, $field);
    }

    //...
}
