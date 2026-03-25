<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\AiDocChunk;

class AiDocChunkController extends BaseController
{
    protected AiDocChunk $data;

    public function __construct(AiDocChunk $data, clsParamRequestEx $objPrEx)
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
