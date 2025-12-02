<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\HrOrgTreeRepositoryInterface;

class HrOrgTreeControllerApi extends BaseApiController
{
    public function __construct(HrOrgTreeRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
