<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class SkusProductVariantOption_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/skus-product-variant-option';

    protected static $web_url_admin = '/admin/skus-product-variant-option';

    protected static $api_url_member = '/api/member-skus-product-variant-option';

    protected static $web_url_member = '/member/skus-product-variant-option';

    // public static $folderParentClass = SkusProductVariantOptionFolderTbl::class;
    public static $modelClass = SkusProductVariantOption::class;

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();

        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //SkusProductVariantOption edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    //...
}
