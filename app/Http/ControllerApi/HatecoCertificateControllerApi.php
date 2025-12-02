<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\HatecoCertificateRepositoryInterface;

class HatecoCertificateControllerApi extends BaseApiController
{
    public function __construct(HatecoCertificateRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
