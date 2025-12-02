<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\MediaItem;

class MediaItemController extends BaseController
{
    protected MediaItem $data;

    public function __construct(MediaItem $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }

    public function tree_index()
    {
        $objMeta = $this->data::getMetaObj();

        return view('admin.default-tree', compact('objMeta'));
    }

    public function getItemsByFolder(){

    }
}
