<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\OcrImage;

class OcrImageController extends BaseController
{
    protected OcrImage $data;

    public function __construct(OcrImage $data, clsParamRequestEx $objPrEx)
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
