<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\EventFaceInfo;

class EventFaceInfoController extends BaseController
{
    protected EventFaceInfo $data;

    public function __construct(EventFaceInfo $data, clsParamRequestEx $objPrEx)
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
