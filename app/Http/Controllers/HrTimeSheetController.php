<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\HrTimeSheet;

class HrTimeSheetController extends BaseController
{
    protected HrTimeSheet $data;

    public function __construct(HrTimeSheet $data, clsParamRequestEx $objPrEx)
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
