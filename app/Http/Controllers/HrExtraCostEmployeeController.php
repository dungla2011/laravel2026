<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\HrExtraCostEmployee;

class HrExtraCostEmployeeController extends BaseController
{
    protected HrExtraCostEmployee $data;

    public function __construct(HrExtraCostEmployee $data, clsParamRequestEx $objPrEx)
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
