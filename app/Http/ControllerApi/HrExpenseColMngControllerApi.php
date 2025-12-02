<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\HrExpenseColMngRepositoryInterface;

class HrExpenseColMngControllerApi extends BaseApiController
{
    public function __construct(HrExpenseColMngRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
