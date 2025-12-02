<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\HrSessionTypeRepositoryInterface;

class HrSessionTypeControllerApi extends BaseApiController
{
    public function __construct(HrSessionTypeRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
