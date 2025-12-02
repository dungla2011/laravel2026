<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\ConferenceCatRepositoryInterface;

class ConferenceCatControllerApi extends BaseApiController
{
    public function __construct(ConferenceCatRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

        parent::__construct();
    }
}
