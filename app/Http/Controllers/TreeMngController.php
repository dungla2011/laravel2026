<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\GiaPha;
use Illuminate\Http\Request;

class TreeMngController extends BaseController
{
    protected GiaPha $data;

    public function __construct(GiaPha $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }

    public function tree_index()
    {
        $objMeta = $this->data::getMetaObj();

        return view('admin.default-tree', compact('objMeta'));
    }

    public function public_tree(Request $rq)
    {
        return view('giapha.giapha_public');
    }

    public function tree_info_item(Request $rq)
    {
        return view('giapha.tieu-su');
    }

    public function vip_account(Request $rq)
    {
        return view('giapha.vip');
    }
}
