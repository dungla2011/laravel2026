<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\CrmAppInfoRepositoryInterface;

class CrmAppInfoControllerApi extends BaseApiController
{
    public function __construct(CrmAppInfoRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

        parent::__construct();
    }
}
