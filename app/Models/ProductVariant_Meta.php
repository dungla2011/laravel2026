<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class ProductVariant_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/product-variant';

    protected static $web_url_admin = '/admin/product-variant';

    protected static $api_url_member = '/api/member-product-variant';

    protected static $web_url_member = '/member/product-variant';

    // public static $folderParentClass = ProductVariantFolderTbl::class;
    public static $modelClass = ProductVariant::class;

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();

        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //ProductVariant edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    //...
}
