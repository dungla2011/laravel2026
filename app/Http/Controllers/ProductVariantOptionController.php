<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\ProductVariantOption;

class ProductVariantOptionController extends BaseController
{
    protected ProductVariantOption $data;

    public function __construct(ProductVariantOption $data, clsParamRequestEx $objPrEx)
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
