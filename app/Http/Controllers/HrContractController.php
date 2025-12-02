<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\HrContract;

class HrContractController extends BaseController
{
    protected HrContract $data;

    public function __construct(HrContract $data, clsParamRequestEx $objPrEx)
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
