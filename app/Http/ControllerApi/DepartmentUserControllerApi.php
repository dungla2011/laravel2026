<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\DepartmentUserRepositoryInterface;

class DepartmentUserControllerApi extends BaseApiController
{
    public function __construct(DepartmentUserRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

        parent::__construct();
    }
}
