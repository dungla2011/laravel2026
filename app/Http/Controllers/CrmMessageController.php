<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\CrmMessage;

class CrmMessageController extends BaseController
{
    protected CrmMessage $data;

    public function __construct(CrmMessage $data, clsParamRequestEx $objPrEx)
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
