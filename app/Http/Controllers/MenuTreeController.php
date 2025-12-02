<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\MenuTree;

class MenuTreeController extends BaseController
{
    protected MenuTree $data;

    public function __construct(MenuTree $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

    }

    public function tree_index()
    {
        return view('admin.menu-tree.tree');
    }
}
