<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\HrJobTitleRepositoryInterface;

class HrJobTitleControllerApi extends BaseApiController
{
    public function __construct(HrJobTitleRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
