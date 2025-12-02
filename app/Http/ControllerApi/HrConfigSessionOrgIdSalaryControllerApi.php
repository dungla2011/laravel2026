<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\HrConfigSessionOrgIdSalaryRepositoryInterface;

class HrConfigSessionOrgIdSalaryControllerApi extends BaseApiController
{
    public function __construct(HrConfigSessionOrgIdSalaryRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
