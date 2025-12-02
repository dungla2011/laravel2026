<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\HrExpenseColMng;

class HrExpenseColMngController extends BaseController
{
    protected HrExpenseColMng $data;

    public function __construct(HrExpenseColMng $data, clsParamRequestEx $objPrEx)
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
