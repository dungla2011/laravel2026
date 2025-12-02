<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\ProductAttributeRepositoryInterface;

class ProductAttributeControllerApi extends BaseApiController
{
    public function __construct(ProductAttributeRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

        parent::__construct();
    }
}
