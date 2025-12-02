<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\HrSalary;

class HrSalaryController extends BaseController
{
    protected HrSalary $data;

    public function __construct(HrSalary $data, clsParamRequestEx $objPrEx)
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
