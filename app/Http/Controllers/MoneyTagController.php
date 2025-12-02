<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\MoneyTag;

class MoneyTagController extends BaseController
{
    protected MoneyTag $data;

    public function __construct(MoneyTag $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
