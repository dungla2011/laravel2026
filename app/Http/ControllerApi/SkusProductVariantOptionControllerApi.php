<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\SkusProductVariantOptionRepositoryInterface;

class SkusProductVariantOptionControllerApi extends BaseApiController
{
    public function __construct(SkusProductVariantOptionRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
