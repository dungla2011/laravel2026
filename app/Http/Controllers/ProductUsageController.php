<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\ProductUsage;

class ProductUsageController extends BaseController
{
    protected ProductUsage $data;

    public function __construct(ProductUsage $data, clsParamRequestEx $objPrEx)
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
