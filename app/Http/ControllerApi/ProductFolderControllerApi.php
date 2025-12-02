<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\ProductFolderRepositoryInterface;

class ProductFolderControllerApi extends BaseApiController
{
    public function __construct(ProductFolderRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
