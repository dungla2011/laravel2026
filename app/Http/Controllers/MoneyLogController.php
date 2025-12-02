<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\MoneyLog;

class MoneyLogController extends BaseController
{
    protected MoneyLog $data;

    public function __construct(MoneyLog $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
