<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\VpsInstance;

class VpsInstanceController extends BaseController
{
    protected VpsInstance $data;

    public function __construct(VpsInstance $data, clsParamRequestEx $objPrEx)
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
