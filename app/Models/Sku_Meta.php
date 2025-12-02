<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class Sku_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/sku';

    protected static $web_url_admin = '/admin/sku';

    protected static $api_url_member = '/api/member-sku';

    protected static $web_url_member = '/member/sku';

    //    public static $folderParentClass = SkuFolderTbl::class;
    public static $modelClass = Sku::class;

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();

        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //Sku edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    public function _product_opt_list($obj, $val)
    {
        $m1 = explode(',', $val);
        $m1 = array_filter($m1);
        $ret = '';

        foreach ($m1 as $optId) {
            $ret .= ' | ';
            if ($obj = ProductVariantOption::find($optId)) {
                if ($obj1 = ProductVariant::find($obj->product_variant_id)) {
                    $ret .= $obj1->name.'';
                }
                $ret .= ' - '.$obj->name;
            }
        }

        return "<div style='font-size: small'> ".trim($ret, ' |').'</div>';

        return $val;
    }

    public function _sku_del($obj, $fid, $field)
    {

        $mm = SkusProductVariantOption::where('sku_id', $obj->id)->get();
        $ret = '';
        foreach ($mm as $skuPro) {
            //$ret .= $skuPro->product_variant_id . " - " . $skuPro->product_variant_options_id ." / "	;
            if ($obj = ProductVariant::find($skuPro->product_variant_id)) {
                $ret .= $obj->name.': ';
            }
            if ($obj = ProductVariantOption::find($skuPro->product_variant_options_id)) {
                $ret .= $obj->name.' , ';
            }
        }

        return trim($ret, ' ,');
    }

    public function _product_id($obj, $fid, $field)
    {

        $pro = Product::find($fid);
        if (! $pro) {
            return null;
        }

        $name = $pro->name;

        return "<div style='font-size: small'> ".trim($name, '').'</div>';

    }

    //...
}
