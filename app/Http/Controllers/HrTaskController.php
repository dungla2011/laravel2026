<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\HrTask;

class HrTaskController extends BaseController
{
    protected HrTask $data;

    public function __construct(HrTask $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }

    public function tree_index()
    {
        $objMeta = $this->data::getMetaObj();

        return view('admin.default-tree', compact('objMeta'));
    }

    public function list_task()
    {
        return $this->getViewLayout();
    }
}
