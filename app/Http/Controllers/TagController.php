<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\DemoTbl;
use App\Models\Tag;

class TagController extends BaseController
{
    protected Tag $data;

    public function __construct(Tag $data, clsParamRequestEx $objPrEx)
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
