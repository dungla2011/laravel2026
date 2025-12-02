<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\HrUserExpenseRepositoryInterface;

class HrUserExpenseControllerApi extends BaseApiController
{
    public function __construct(HrUserExpenseRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
