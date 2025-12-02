<?php

namespace App\Models;

use App\Components\Helper1;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Common\Database\MetaOfTableInDb;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Meta;

/**
 * ABC123
 * @param null $objData
 */
class ProductAttribute_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = "/api/product-attribute";
    protected static $web_url_admin = "/admin/product-attribute";

    protected static $api_url_member = "/api/member-product-attribute";
    protected static $web_url_member = "/member/product-attribute";

    //public static $folderParentClass = ProductAttributeFolderTbl::class;
    public static $modelClass = ProductAttribute::class;

    /**
     * @param $field
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field){

        $objMeta = new MetaOfTableInDb();

        //Riêng Data type của Field, Lấy ra các field datatype mặc định
        //Nếu có thay đổi sẽ SET bên dưới
        $objSetDefault = new MetaOfTableInDb();
        $objSetDefault->setDefaultMetaTypeField($field);

        $objMeta->dataType = $objSetDefault->dataType;

        if($field == 'status'){
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
//
//        if($field == 'attribute_name'){
//            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;
//        }

        if($field == 'product_id'){
//            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;
        }


        if($field == 'tag_list_id'){
            $objMeta->join_api_field = 'name';
//          $objMeta->join_func = 'joinTags';
            //ProductAttribute edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }
        if($field == 'parent_extra' || $field == 'parent_all' ){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_MULTI_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\ProductAttributeFolderTbl::joinFuncPathNameFullTree';
        }

        if($field == 'parent_id'){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\ProductAttributeFolderTbl::joinFuncPathNameFullTree';
        }

        //Nếu không set thì lấy của parent default nếu có
        if(!$objMeta->dataType)
            if($ret = parent::getHardCodeMetaObj($field))
                return $ret;

        return $objMeta;
    }
    function _image_list1($obj, $val, $field){
        return Helper1::imageShow1($obj, $val, $field);
    }


    function _attribute_name($obj, $val)
    {

        if(!$obj)
            return;

        $prodId = $obj->product_id;
        if($prodId && $product = Product::find($prodId)){

            return Product_Meta::getArrayAttributeOfProduct($product)[$val];
            return  $product->type;
        }

    }




    public function extraJsInclude()
    {
        ?>

        <script>

            document.addEventListener('DOMContentLoaded', function() {
                const inputs = document.querySelectorAll('input.product_id');
                const colorMap = {};
                let colorIndex = 0;
                const colors = ['red', 'blue', 'green', 'purple']; // Add more colors as needed

                inputs.forEach(input => {
                    const value = input.value;
                    if (!colorMap[value]) {
                        colorMap[value] = colors[colorIndex % colors.length];
                        colorIndex++;
                    }
                    input.style.color = colorMap[value];

                    // Apply the same color to sibling elements with the class 'join_val_text'
                    const siblings = input.parentElement.querySelectorAll('.join_val_text');
                    siblings.forEach(sibling => {
                        sibling.style.color = colorMap[value];
                    });
                });
            });

        </script>
<?php
    }


    function _product_id($obj, $val)
    {
        if($product = Product::find($val)){
            $price = number_formatvn0($product->price);
            return "<span style='margin-left: 10px; font-size: 90%'> <a href='/admin/product/edit/$val' target='_blank'>[E] </a> ($product->id) $product->name , $price đ</span> ";
        }
//        return;
//        $mm = Product::orderBy('name','asc')->get();
//
//        $m1 = [0 => " --- Chọn ---"];
//        foreach ($mm AS $prod){
//            $m1[$prod->id] = " ($prod->id) $prod->name - " . number_formatvn0($prod->price ) . " Đ ";
//        }
//
//        return $m1;
    }

    public function getSqlOrJoinExtraIndex(\Illuminate\Database\Eloquent\Builder &$x = null, $getSelect = 0)
    {
        //Join voi product va chi lay ra cac product co status = 1
        $x->leftJoin('products', 'products.id', '=', 'product_attributes.product_id');

    }

    public function getFullSearchJoinField()
    {
        return ['products.name'  => "like"];
    }

    //...

    public function executeBeforeIndex($param = null)
    {

        //Liet ke tat ca product dang duoc Active
        //Thêm thuộc tính vô
        $mm = Product::where("status", 1)->get();
        foreach ($mm AS $prod){
            $mAttribute = array_keys(Product_Meta::getArrayAttributeOfProduct($prod));

            if($mAttribute)
            foreach ($mAttribute AS $key => $attName){
//                dump($attName);
//
//                dump($attName);

                if(!ProductAttribute::where("product_id", $prod->id)->where('attribute_name', $attName)->first()){

                    $obj = new ProductAttribute();
                    $obj->product_id = $prod->id;
                    $obj->attribute_name = $attName;
                    $obj->save();
                }
            }
        }


    }


}
