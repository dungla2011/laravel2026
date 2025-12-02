<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\demoMg;

class DemoMgController extends BaseController
{
    protected demoMg $data;

    public function __construct(demoMg $data, clsParamRequestEx $objPrEx)
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
