<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\OrderShipRepositoryInterface;

class OrderShipControllerApi extends BaseApiController
{
    public function __construct(OrderShipRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
