<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\CrmMessages;

class CrmMessagesController extends BaseController
{
    protected CrmMessages $data;

    public function __construct(CrmMessages $data, clsParamRequestEx $objPrEx)
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
