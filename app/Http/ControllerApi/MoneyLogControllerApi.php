<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\MoneyLogRepositoryInterface;

class MoneyLogControllerApi extends BaseApiController
{
    public function __construct(MoneyLogRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
