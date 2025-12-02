<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\FileUpload;

class FileUploadController extends BaseController
{
    protected FileUpload $data;

    public function __construct(FileUpload $data, clsParamRequestEx $objPrEx)
    {

        $this->data = $data;
        $this->objParamEx = $objPrEx;

    }

    //    public function index_admin(){
    //        return view("admin.file.index");
    //    }

    public function tree_index()
    {
        return view('admin.demo-api.folder-tree');
    }

    public function upload()
    {
        return view('admin.file.upload');
    }
}
