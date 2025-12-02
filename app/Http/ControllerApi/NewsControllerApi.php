<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\NewsRepositoryInterface;

class NewsControllerApi extends BaseApiController
{
    public function __construct(NewsRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
