<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\QuizSessionInfoTest;

class QuizSessionInfoTestController extends BaseController
{
    protected QuizSessionInfoTest $data;

    public function __construct(QuizSessionInfoTest $data, clsParamRequestEx $objPrEx)
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
