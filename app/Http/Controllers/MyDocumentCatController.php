<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\MyDocumentCat;

class MyDocumentCatController extends BaseController
{
    protected MyDocumentCat $data;

    public function __construct(MyDocumentCat $data, clsParamRequestEx $objPrEx)
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
