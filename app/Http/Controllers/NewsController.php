<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\News;

class NewsController extends BaseController
{
    protected News $data;

    public function __construct(News $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
