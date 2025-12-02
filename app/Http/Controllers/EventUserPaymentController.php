<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\EventUserPayment;

class EventUserPaymentController extends BaseController
{
    protected EventUserPayment $data;

    public function __construct(EventUserPayment $data, clsParamRequestEx $objPrEx)
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
