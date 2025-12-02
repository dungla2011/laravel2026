<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\Product;

class ProductController extends BaseController
{
    protected Product $data;

    public function __construct(Product $data, clsParamRequestEx $objPrEx)
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
