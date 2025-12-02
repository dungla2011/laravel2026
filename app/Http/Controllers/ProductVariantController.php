<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\ProductVariant;

class ProductVariantController extends BaseController
{
    protected ProductVariant $data;

    public function __construct(ProductVariant $data, clsParamRequestEx $objPrEx)
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
