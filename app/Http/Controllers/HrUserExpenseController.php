<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\HrUserExpense;

class HrUserExpenseController extends BaseController
{
    protected HrUserExpense $data;

    public function __construct(HrUserExpense $data, clsParamRequestEx $objPrEx)
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
