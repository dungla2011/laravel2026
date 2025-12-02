<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\NewsFolder;

class NewsFolderController extends BaseController
{
    protected NewsFolder $data;

    public function __construct(NewsFolder $data, clsParamRequestEx $objPrEx)
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
