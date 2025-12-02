<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\ProductAttribute;

class ProductAttributeController extends BaseController
{
    protected ProductAttribute $data;

    public function __construct(ProductAttribute $data, clsParamRequestEx $objPrEx)
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
