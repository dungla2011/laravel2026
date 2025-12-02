<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\CrmMessageGroup;

class CrmMessageGroupController extends BaseController
{
    protected CrmMessageGroup $data;

    public function __construct(CrmMessageGroup $data, clsParamRequestEx $objPrEx)
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
