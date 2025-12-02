<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\PlanDefineValue;

class PlanDefineValueController extends BaseController
{
    protected PlanDefineValue $data;

    public function __construct(PlanDefineValue $data, clsParamRequestEx $objPrEx)
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
