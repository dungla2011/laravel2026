<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\SiteMngRepositoryInterface;

class SiteMngControllerApi extends BaseApiController
{
    public function __construct(SiteMngRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
