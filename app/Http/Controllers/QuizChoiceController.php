<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\QuizChoice;

class QuizChoiceController extends BaseController
{
    protected QuizChoice $data;

    public function __construct(QuizChoice $data, clsParamRequestEx $objPrEx)
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
