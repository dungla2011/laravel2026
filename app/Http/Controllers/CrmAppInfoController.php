<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\CrmAppInfo;

class CrmAppInfoController extends BaseController
{
    protected CrmAppInfo $data;

    public function __construct(CrmAppInfo $data, clsParamRequestEx $objPrEx)
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
