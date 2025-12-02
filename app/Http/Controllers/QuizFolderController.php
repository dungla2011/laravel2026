<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\QuizFolder;

class QuizFolderController extends BaseController
{
    protected QuizFolder $data;

    public function __construct(QuizFolder $data, clsParamRequestEx $objPrEx)
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
