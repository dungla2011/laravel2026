<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\Todo2;

class Todo2Controller extends BaseController
{
    protected Todo2 $data;

    public function __construct(Todo2 $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
