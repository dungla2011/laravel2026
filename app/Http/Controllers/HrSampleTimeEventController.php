<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\HrSampleTimeEvent;

class HrSampleTimeEventController extends BaseController
{
    protected HrSampleTimeEvent $data;

    public function __construct(HrSampleTimeEvent $data, clsParamRequestEx $objPrEx)
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
