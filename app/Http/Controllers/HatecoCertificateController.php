<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\HatecoCertificate;

class HatecoCertificateController extends BaseController
{
    protected HatecoCertificate $data;

    public function __construct(HatecoCertificate $data, clsParamRequestEx $objPrEx)
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
