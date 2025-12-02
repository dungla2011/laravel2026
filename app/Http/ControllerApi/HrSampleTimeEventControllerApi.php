<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\HrSampleTimeEventRepositoryInterface;

class HrSampleTimeEventControllerApi extends BaseApiController
{
    public function __construct(HrSampleTimeEventRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
