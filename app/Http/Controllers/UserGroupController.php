<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\UserGroup;

class UserGroupController extends BaseController
{
    protected UserGroup $data;

    public function __construct(UserGroup $data, clsParamRequestEx $objPrEx)
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
