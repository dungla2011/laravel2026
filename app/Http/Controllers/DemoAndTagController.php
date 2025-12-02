<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\DemoAndTagTbl;

class DemoAndTagController extends BaseController
{
    protected DemoAndTagTbl $data;

    public function __construct(DemoAndTagTbl $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

    }
}
