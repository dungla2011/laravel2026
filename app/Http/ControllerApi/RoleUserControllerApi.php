<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\RoleUserRepositoryInterface;

class RoleUserControllerApi extends BaseApiController
{
    public function __construct(RoleUserRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
