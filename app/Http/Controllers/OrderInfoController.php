<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\OrderInfo;

class OrderInfoController extends BaseController
{
    protected OrderInfo $data;

    public function __construct(OrderInfo $data, clsParamRequestEx $objPrEx)
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
