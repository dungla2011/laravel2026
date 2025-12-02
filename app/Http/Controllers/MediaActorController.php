<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\MediaActor;

class MediaActorController extends BaseController
{
    protected MediaActor $data;

    public function __construct(MediaActor $data, clsParamRequestEx $objPrEx)
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
