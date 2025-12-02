<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\EventAndUserRepositoryInterface;

class EventAndUserControllerApi extends BaseApiController
{
    public function __construct(EventAndUserRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
//        $objPrEx->need_set_uid = 0;
        $this->objParamEx = $objPrEx;

        parent::__construct();
    }
}
