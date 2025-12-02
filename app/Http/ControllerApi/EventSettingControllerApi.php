<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\EventSettingRepositoryInterface;

class EventSettingControllerApi extends BaseApiController
{
    public function __construct(EventSettingRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {

        $this->data = $data;
        $this->objParamEx = $objPrEx;
        $objPrEx->need_set_uid = 0;

        parent::__construct();
    }
}
