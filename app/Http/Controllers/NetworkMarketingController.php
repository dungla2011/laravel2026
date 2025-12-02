<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\NetworkMarketing;

class NetworkMarketingController extends BaseController
{
    protected NetworkMarketing $data;

    public function __construct(NetworkMarketing $data, clsParamRequestEx $objPrEx)
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
