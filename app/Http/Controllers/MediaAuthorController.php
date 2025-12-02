<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\MediaAuthor;

class MediaAuthorController extends BaseController
{
    protected MediaAuthor $data;

    public function __construct(MediaAuthor $data, clsParamRequestEx $objPrEx)
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
