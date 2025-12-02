<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\QuizUserAnswer;

class QuizUserAnswerController extends BaseController
{
    protected QuizUserAnswer $data;

    public function __construct(QuizUserAnswer $data, clsParamRequestEx $objPrEx)
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
