<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\PlanCostItem;

class PlanCostItemController extends BaseController
{
    protected PlanCostItem $data;

    public function __construct(PlanCostItem $data, clsParamRequestEx $objPrEx)
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
