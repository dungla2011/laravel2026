<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\EventUserInfoRepositoryInterface;

class EventUserInfoControllerApi extends BaseApiController
{
    public function __construct(EventUserInfoRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $objPrEx->need_set_uid = 0;
        $this->objParamEx = $objPrEx;
    }
}
