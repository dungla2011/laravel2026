<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\TypingLesson;

class TypingLessonController extends BaseController
{
    protected TypingLesson $data;

    public function __construct(TypingLesson $data, clsParamRequestEx $objPrEx)
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
