<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\ChangeLog;

class ChangeLogController extends BaseController
{
    protected ChangeLog $data;

    public function __construct(ChangeLog $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
        parent::__construct();
    }

    public function tree_index()
    {
        $objMeta = $this->data::getMetaObj();

        return view('admin.default-tree', compact('objMeta'));
    }
}
