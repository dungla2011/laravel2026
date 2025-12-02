<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\TransportInfo;

class TransportInfoController extends BaseController
{
    protected TransportInfo $data;

    public function __construct(TransportInfo $data, clsParamRequestEx $objPrEx)
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
