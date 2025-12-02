<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\AssetCategory;

class AssetCategoryController extends BaseController
{
    protected AssetCategory $data;

    public function __construct(AssetCategory $data, clsParamRequestEx $objPrEx)
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
