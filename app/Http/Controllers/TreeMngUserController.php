<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\GiaPhaUser;

class TreeMngUserController extends BaseController
{
    protected GiaPhaUser $data;

    public function __construct(GiaPhaUser $data, clsParamRequestEx $objPrEx)
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
