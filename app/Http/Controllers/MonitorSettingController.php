<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\MonitorSetting;

class MonitorSettingController extends BaseController
{
    protected MonitorSetting $data;

    public function __construct(MonitorSetting $data, clsParamRequestEx $objPrEx)
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
