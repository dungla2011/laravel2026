<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\PartnerInfoRepositoryInterface;

class PartnerInfoControllerApi extends BaseApiController
{
    public function __construct(PartnerInfoRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
