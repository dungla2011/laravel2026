<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\SkusProductVariantOption;

class SkusProductVariantOptionController extends BaseController
{
    protected SkusProductVariantOption $data;

    public function __construct(SkusProductVariantOption $data, clsParamRequestEx $objPrEx)
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
