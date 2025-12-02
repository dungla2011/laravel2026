<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\ConferenceInfo;

class ConferenceInfoController extends BaseController
{
    protected ConferenceInfo $data;

    public function __construct(ConferenceInfo $data, clsParamRequestEx $objPrEx)
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
