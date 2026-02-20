<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\VpsUsage;

class VpsUsageController extends BaseController
{
    protected VpsUsage $data;

    public function __construct(VpsUsage $data, clsParamRequestEx $objPrEx)
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
