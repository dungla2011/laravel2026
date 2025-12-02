<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\MoneyTagRepositoryInterface;

class MoneyTagControllerApi extends BaseApiController
{
    public function __construct(MoneyTagRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
