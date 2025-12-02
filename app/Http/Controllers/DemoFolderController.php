<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\DemoFolderTbl;

class DemoFolderController extends BaseController
{
    protected DemoFolderTbl $data;

    public function __construct(DemoFolderTbl $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
        parent::__construct();
    }

    public function tree_index()
    {
        return view('admin.demo-api.folder-tree');
    }
}
