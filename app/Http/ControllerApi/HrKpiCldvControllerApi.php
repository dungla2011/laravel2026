<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\HrKpiCldvRepositoryInterface;

class HrKpiCldvControllerApi extends BaseApiController
{
    public function __construct(HrKpiCldvRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
