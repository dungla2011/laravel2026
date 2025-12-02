<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\DonViHanhChinh;

class DonViHanhChinhController extends BaseController
{
    protected DonViHanhChinh $data;

    public function __construct(DonViHanhChinh $data, clsParamRequestEx $objPrEx)
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
