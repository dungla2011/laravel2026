<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\PlanName;

class PlanNameController extends BaseController
{
    protected PlanName $data;

    public function __construct(PlanName $data, clsParamRequestEx $objPrEx)
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
