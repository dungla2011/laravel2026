<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\Asset;

class AssetsController extends BaseController
{
    protected Asset $data;

    public function __construct(Asset $data, clsParamRequestEx $objPrEx)
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
