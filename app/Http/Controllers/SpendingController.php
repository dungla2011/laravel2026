<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\Spending;

class SpendingController extends BaseController
{
    protected Spending $data;

    public function __construct(Spending $data, clsParamRequestEx $objPrEx)
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
