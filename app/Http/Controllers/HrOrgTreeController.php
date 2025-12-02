<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\HrOrgTree;

class HrOrgTreeController extends BaseController
{
    protected HrOrgTree $data;

    public function __construct(HrOrgTree $data, clsParamRequestEx $objPrEx)
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
