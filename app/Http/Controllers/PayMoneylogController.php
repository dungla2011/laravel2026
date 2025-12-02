<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\PayMoneylog;

class PayMoneylogController extends BaseController
{
    protected PayMoneylog $data;

    public function __construct(PayMoneylog $data, clsParamRequestEx $objPrEx)
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
