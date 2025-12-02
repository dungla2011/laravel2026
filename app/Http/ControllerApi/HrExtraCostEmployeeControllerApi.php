<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\HrExtraCostEmployeeRepositoryInterface;

class HrExtraCostEmployeeControllerApi extends BaseApiController
{
    public function __construct(HrExtraCostEmployeeRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
