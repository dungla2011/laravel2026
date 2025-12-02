<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\MonitorItem;

class MonitorItemController extends BaseController
{
    protected MonitorItem $data;

    public function __construct(MonitorItem $data, clsParamRequestEx $objPrEx)
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
