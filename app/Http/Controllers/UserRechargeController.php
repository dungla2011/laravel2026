<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\UserRecharge;

class UserRechargeController extends BaseController
{
    protected UserRecharge $data;

    public function __construct(UserRecharge $data, clsParamRequestEx $objPrEx)
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
