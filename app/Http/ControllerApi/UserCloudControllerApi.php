<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\UserCloudRepositoryInterface;

class UserCloudControllerApi extends BaseApiController
{
    public function __construct(UserCloudRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

    }
}
