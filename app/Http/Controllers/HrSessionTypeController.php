<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\HrSessionType;

class HrSessionTypeController extends BaseController
{
    protected HrSessionType $data;

    public function __construct(HrSessionType $data, clsParamRequestEx $objPrEx)
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
