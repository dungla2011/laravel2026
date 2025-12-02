<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\EventPaymentRepositoryInterface;

class EventPaymentControllerApi extends BaseApiController
{
    public function __construct(EventPaymentRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

        parent::__construct();
    }
}
