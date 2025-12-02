<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\ProductVariantOptionRepositoryInterface;

class ProductVariantOptionControllerApi extends BaseApiController
{
    public function __construct(ProductVariantOptionRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
