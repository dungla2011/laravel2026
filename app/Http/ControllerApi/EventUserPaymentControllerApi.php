<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\EventUserPaymentRepositoryInterface;

class EventUserPaymentControllerApi extends BaseApiController
{
    public function __construct(EventUserPaymentRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

        parent::__construct();
    }
}
