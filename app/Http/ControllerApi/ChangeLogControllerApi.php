<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\ChangeLogRepositoryInterface;

class ChangeLogControllerApi extends BaseApiController
{
    public function __construct(ChangeLogRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
