<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\QuizQuestion;

class QuizQuestionController extends BaseController
{
    protected QuizQuestion $data;

    public function __construct(QuizQuestion $data, clsParamRequestEx $objPrEx)
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
