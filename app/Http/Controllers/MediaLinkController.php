<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\MediaLink;

class MediaLinkController extends BaseController
{
    protected MediaLink $data;

    public function __construct(MediaLink $data, clsParamRequestEx $objPrEx)
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
