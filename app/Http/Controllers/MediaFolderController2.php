<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\MediaFolder2;

class MediaFolderController2 extends BaseController
{
    protected MediaFolder $data;

    public function __construct(MediaFolder $data, clsParamRequestEx $objPrEx)
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
