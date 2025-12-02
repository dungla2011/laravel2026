<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\HrOrgSettingRepositoryInterface;

class HrOrgSettingControllerApi extends BaseApiController
{
    public function __construct(HrOrgSettingRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
