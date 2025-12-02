<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\HrConfigSessionOrgIdSalary;

class HrConfigSessionOrgIdSalaryController extends BaseController
{
    protected HrConfigSessionOrgIdSalary $data;

    public function __construct(HrConfigSessionOrgIdSalary $data, clsParamRequestEx $objPrEx)
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
