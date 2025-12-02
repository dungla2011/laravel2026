<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\DepartmentEventRepositoryInterface;

class DepartmentEventControllerApi extends BaseApiController
{
    public function __construct(DepartmentEventRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

        parent::__construct();
    }
}
