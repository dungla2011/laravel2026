<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\Cart;

class CartController extends BaseController
{
    protected Cart $data;

    public function __construct(Cart $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }

    public function tree_index()
    {
        $objMeta = $this->data::getMetaObj();

        return view('admin.default-tree', compact('objMeta'));
    }
}
