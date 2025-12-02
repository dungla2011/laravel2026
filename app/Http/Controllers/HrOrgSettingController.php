<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\HrOrgSetting;

class HrOrgSettingController extends BaseController
{
    protected HrOrgSetting $data;

    public function __construct(HrOrgSetting $data, clsParamRequestEx $objPrEx)
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
