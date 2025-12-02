<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\HrSalaryRepositoryInterface;

class HrSalaryControllerApi extends BaseApiController
{
    public function __construct(HrSalaryRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
