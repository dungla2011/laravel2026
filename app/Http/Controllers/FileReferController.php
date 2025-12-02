<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\DownloadLog;
use App\Models\FileRefer;

class FileReferController extends BaseController
{
    protected FileRefer $data;

    public function __construct(FileRefer $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }

    function index222()
    {

        $mmFid = [];
        //Update luowt tai all:
        $mm = DownloadLog::all();
        foreach ($mm AS $dlObj){
            if($fr = FileRefer::find($dlObj->file_refer_id)){
                if(!isset($mmFid[$fr->id]))
                    $mmFid[$fr->id] = 0;
                if($dlObj->count_dl)
                    $mmFid[$fr->id]+= $dlObj->count_dl;
            }
        }

        foreach ($mmFid AS $fidRf => $cld){
            if($fr = FileRefer::find($fidRf)){
                $fr->count_dl = $cld;
                $fr->save();
            }
        }


        parent::index();
    }

    public function tree_index()
    {
        $objMeta = $this->data::getMetaObj();

        return view('admin.default-tree', compact('objMeta'));
    }
}
