<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\PartnerInfo;

class PartnerInfoController extends BaseController
{
    protected PartnerInfo $data;

    public function __construct(PartnerInfo $data, clsParamRequestEx $objPrEx)
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
