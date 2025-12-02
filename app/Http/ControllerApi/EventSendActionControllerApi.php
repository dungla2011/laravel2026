<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\EventSendActionRepositoryInterface;

class EventSendActionControllerApi extends BaseApiController
{
    public function __construct(EventSendActionRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        //Member không cần limit user, vi se limit theo department
        $objPrEx->need_set_uid = 0;
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
