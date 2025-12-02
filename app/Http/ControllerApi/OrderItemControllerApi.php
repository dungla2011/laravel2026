<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\OrderItemRepositoryInterface;

class OrderItemControllerApi extends BaseApiController
{
    public function __construct(OrderItemRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
