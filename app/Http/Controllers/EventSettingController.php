<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\EventSetting;

class EventSettingController extends BaseController
{
    protected EventSetting $data;

    public function __construct(EventSetting $data, clsParamRequestEx $objPrEx)
    {
        $objPrEx->need_set_uid = 0;

        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }

    public function tree_index()
    {
        $objMeta = $this->data::getMetaObj();

        return view('admin.default-tree', compact('objMeta'));
    }
}
