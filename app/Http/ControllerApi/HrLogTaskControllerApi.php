<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\HrLogTaskRepositoryInterface;

class HrLogTaskControllerApi extends BaseApiController
{
    public function __construct(HrLogTaskRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
