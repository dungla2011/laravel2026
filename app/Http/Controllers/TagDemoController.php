<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\DemoTbl;
use App\Models\Tag;
use App\Models\TagDemo;

class TagDemoController extends BaseController
{
    protected TagDemo $data;

    public function __construct(TagDemo $data, clsParamRequestEx $objPrEx)
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
