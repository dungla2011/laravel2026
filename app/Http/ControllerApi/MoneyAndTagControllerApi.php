<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\MoneyAndTagRepositoryInterface;

class MoneyAndTagControllerApi extends BaseApiController
{
    public function __construct(MoneyAndTagRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
