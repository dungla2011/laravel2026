<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\TypingTestResult;

class TypingTestResultController extends BaseController
{
    protected TypingTestResult $data;

    public function __construct(TypingTestResult $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }

    public function tree_index()
    {
        $objMeta = $this->data::getMetaObj();

        return view('admin.default-tree', compact('objMeta'));
    }

    public function history()
    {

        return view('index.tap-danh-may.history');

    }
}
