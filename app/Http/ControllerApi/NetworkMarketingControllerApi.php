<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\NetworkMarketingRepositoryInterface;

class NetworkMarketingControllerApi extends BaseApiController
{
    public function __construct(NetworkMarketingRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
