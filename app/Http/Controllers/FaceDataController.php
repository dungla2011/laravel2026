<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\FaceData;

class FaceDataController extends BaseController
{
    protected FaceData $data;

    public function __construct(FaceData $data, clsParamRequestEx $objPrEx)
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
