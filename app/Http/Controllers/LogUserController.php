<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\LogUser;

class LogUserController extends BaseController
{
    protected LogUser $data;

    public function __construct(LogUser $data, clsParamRequestEx $objPrEx)
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
