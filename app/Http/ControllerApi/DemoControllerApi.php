<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\DemoRepositoryInterface;

class DemoControllerApi extends BaseApiController
{
    public function __construct(DemoRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

        parent::__construct();
    }
}
