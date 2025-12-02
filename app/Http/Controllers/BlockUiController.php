<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\BlockUi;

class BlockUiController extends BaseController
{
    protected BlockUi $data;

    public function __construct(BlockUi $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
        parent::__construct();
    }

    public function index1()
    {
        //        $databaseName = \DB::connection()->getDatabaseName();
        //        die('xxxxxx . ' . $databaseName);
    }

    public function tree_index()
    {
        $objMeta = $this->data::getMetaObj();

        return view('admin.default-tree', compact('objMeta'));
    }
}
