<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\MyDocument;

class MyDocumentController extends BaseController
{
    protected MyDocument $data;

    public function __construct(MyDocument $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }

    public function tree_index()
    {
        $objMeta = $this->data::getMetaObj();

        return view('admin.default-tree', compact('objMeta'));
    }

    public function item()
    {
        return $this->getViewLayout();
//        if(isDebugIp())
//
//        else
//        return view('index.toan_tin.tai-lieu-item');
    }

    public function folder()
    {
        return $this->getViewLayout();
//        if(isDebugIp())
//
//        else
//            return view('index.toan_tin.tai-lieu-folder');

    }
}
