<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\ProductUsageRepositoryInterface;

class ProductUsageControllerApi extends BaseApiController
{
    public function __construct(ProductUsageRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

        parent::__construct();
    }
}
