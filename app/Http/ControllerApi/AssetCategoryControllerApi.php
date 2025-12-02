<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\AssetCategoryRepositoryInterface;

class AssetCategoryControllerApi extends BaseApiController
{
    public function __construct(AssetCategoryRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

        parent::__construct();
    }
}
