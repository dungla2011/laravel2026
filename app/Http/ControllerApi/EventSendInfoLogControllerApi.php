<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\EventSendInfoLogRepositoryInterface;

class EventSendInfoLogControllerApi extends BaseApiController
{
    public function __construct(EventSendInfoLogRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        //Member không cần limit user, vi se limit theo department
        $objPrEx->need_set_uid = 0;
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
