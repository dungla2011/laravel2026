<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\PlanCostItemRepositoryInterface;

class PlanCostItemControllerApi extends BaseApiController
{
    public function __construct(PlanCostItemRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

        parent::__construct();
    }
}
