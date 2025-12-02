<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\HrKpiCldv;

class HrKpiCldvController extends BaseController
{
    protected HrKpiCldv $data;

    public function __construct(HrKpiCldv $data, clsParamRequestEx $objPrEx)
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
