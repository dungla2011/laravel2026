<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\OrderShip;

class OrderShipController extends BaseController
{
    protected OrderShip $data;

    public function __construct(OrderShip $data, clsParamRequestEx $objPrEx)
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
