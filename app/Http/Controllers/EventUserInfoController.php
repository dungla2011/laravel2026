<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\EventUserInfo;

class EventUserInfoController extends BaseController
{
    protected EventUserInfo $data;

    public function __construct(EventUserInfo $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $objPrEx->need_set_uid = 0;
        $this->objParamEx = $objPrEx;
        parent::__construct();
    }


    public function tree_index()
    {
        $objMeta = $this->data::getMetaObj();

        return view('admin.default-tree', compact('objMeta'));
    }
}
