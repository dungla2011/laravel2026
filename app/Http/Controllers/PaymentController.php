<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\Payment;

class PaymentController extends BaseController
{
    protected Payment $data;

    public function __construct(Payment $data, clsParamRequestEx $objPrEx)
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
