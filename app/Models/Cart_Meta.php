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
class Cart_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = "/api/cart";
    protected static $web_url_admin = "/admin/cart";

    protected static $api_url_member = "/api/member-cart";
    protected static $web_url_member = "/member/cart";

    //public static $folderParentClass = CartFolderTbl::class;
    public static $modelClass = Cart::class;

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
        if($field == 'tag_list_id'){
            $objMeta->join_api_field = 'name';
//          $objMeta->join_func = 'joinTags';
            //Cart edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }
        if($field == 'parent_extra' || $field == 'parent_all' ){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_MULTI_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\CartFolderTbl::joinFuncPathNameFullTree';
        }

        if($field == 'parent_id'){
            $objMeta->dataType = DEF_DATA_TYPE_TREE_SELECT;
            $objMeta->join_api = '/api/need_define';
//            $objMeta->join_func = 'App\Models\CartFolderTbl::joinFuncPathNameFullTree';
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


    function _name($obj, $val, $field)
    {
        // Tim cac cart item cua cart nay
        $cart_id = $obj->id;
        $model = new CartItem();
        $cart_items = $model::where(['cart_id' => $cart_id])->get();
        $totalQuantity = 0;
        $totalPrice = 0;
        $str = '';

        // Dua ra Table cart item, gia, so luong, tong tien
        $str .= "<table class='table'>";
        $str .= "<thead><tr><th>Product Name</th><th>Price</th><th>Quantity</th><th>Total Price</th></tr></thead>";
        $str .= "<tbody>";
        foreach ($cart_items as $item) {
            $product = Product::find($item->product_id);
            $itemTotalPrice = $product->price * $item->quantity;
            $str .= "<tr><td> (MSP: $product->id) $product->name</td><td>$product->price</td><td>$item->quantity</td><td>$itemTotalPrice</td></tr>";
            $totalQuantity += $item->quantity;
            $totalPrice += $itemTotalPrice;
        }
        $str .= "<tr><td><strong>Total</strong></td><td></td><td><strong>$totalQuantity</strong></td><td><strong>$totalPrice</strong></td></tr>";
        $str .= "</tbody></table>";

        return "<div class='m-2'> $str </div>";
    }




}
