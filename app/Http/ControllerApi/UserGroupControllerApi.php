<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\UserGroupRepositoryInterface;

class UserGroupControllerApi extends BaseApiController
{
    public function __construct(UserGroupRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
