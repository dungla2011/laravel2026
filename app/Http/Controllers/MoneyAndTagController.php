<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\MoneyAndTag;

class MoneyAndTagController extends BaseController
{
    protected MoneyAndTag $data;

    public function __construct(MoneyAndTag $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
