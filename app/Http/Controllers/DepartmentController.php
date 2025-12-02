<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\Department;

class DepartmentController extends BaseController
{
    protected Department $data;

    public function __construct(Department $data, clsParamRequestEx $objPrEx)
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
