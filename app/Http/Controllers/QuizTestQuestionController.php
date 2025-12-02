<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\QuizTestQuestion;

class QuizTestQuestionController extends BaseController
{
    protected QuizTestQuestion $data;

    public function __construct(QuizTestQuestion $data, clsParamRequestEx $objPrEx)
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
