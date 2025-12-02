<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\DemoTbl;

class DemoUseApiController extends BaseController
{
    protected DemoTbl $data;

    public function __construct(DemoTbl $data, clsParamRequestEx $objPrEx)
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
