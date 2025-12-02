<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\FolderFile;

class FolderFileController extends BaseController
{
    protected FolderFile $data;

    public function __construct(FolderFile $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

    }

    public function tree_index()
    {
        return view('admin.folder-file.tree');
    }
}
