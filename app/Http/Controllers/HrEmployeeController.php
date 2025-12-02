<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\HrEmployee;

class HrEmployeeController extends BaseController
{
    protected HrEmployee $data;

    public function __construct(HrEmployee $data, clsParamRequestEx $objPrEx)
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
