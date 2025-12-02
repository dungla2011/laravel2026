<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\EventUserGroupRepositoryInterface;

class EventUserGroupControllerApi extends BaseApiController
{
    public function __construct(EventUserGroupRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $objPrEx->need_set_uid = 0;
        $this->objParamEx = $objPrEx;
    }
}
