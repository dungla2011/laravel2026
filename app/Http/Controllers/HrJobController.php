<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\HrJob;

class HrJobController extends BaseController
{
    protected HrJob $data;

    public function __construct(HrJob $data, clsParamRequestEx $objPrEx)
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
