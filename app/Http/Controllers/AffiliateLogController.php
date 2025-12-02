<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\AffiliateLog;
use App\Models\SiteMng;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AffiliateLogController extends BaseController
{
    protected AffiliateLog $data;

    public function __construct(AffiliateLog $data, clsParamRequestEx $objPrEx)
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
