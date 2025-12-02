<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\PlanDefineRepositoryInterface;

class PlanDefineControllerApi extends BaseApiController
{
    public function __construct(PlanDefineRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

        parent::__construct();
    }
}
