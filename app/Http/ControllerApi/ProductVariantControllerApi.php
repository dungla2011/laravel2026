<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\ProductVariantRepositoryInterface;

class ProductVariantControllerApi extends BaseApiController
{
    public function __construct(ProductVariantRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
