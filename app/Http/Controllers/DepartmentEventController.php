<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\DepartmentEvent;

class DepartmentEventController extends BaseController
{
    protected DepartmentEvent $data;

    public function __construct(DepartmentEvent $data, clsParamRequestEx $objPrEx)
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
