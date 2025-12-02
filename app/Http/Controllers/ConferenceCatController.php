<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\ConferenceCat;

class ConferenceCatController extends BaseController
{
    protected ConferenceCat $data;

    public function __construct(ConferenceCat $data, clsParamRequestEx $objPrEx)
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
