<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class ProductVariantOption_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/product-variant-option';

    protected static $web_url_admin = '/admin/product-variant-option';

    protected static $api_url_member = '/api/member-product-variant-option';

    protected static $web_url_member = '/member/product-variant-option';

    //  public static $folderParentClass = ProductVariantOptionFolderTbl::class;
    public static $modelClass = ProductVariantOption::class;

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();

        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //ProductVariantOption edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    //...
}
