<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\CloudServerRepositoryInterface;

class CloudServerControllerApi extends BaseApiController
{
    public function __construct(CloudServerRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

        parent::__construct();
    }
}
