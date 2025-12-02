<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\DepartmentUser;

class DepartmentUserController extends BaseController
{
    protected DepartmentUser $data;

    public function __construct(DepartmentUser $data, clsParamRequestEx $objPrEx)
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
