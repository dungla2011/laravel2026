<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\Todo2RepositoryInterface;

class Todo2ControllerApi extends BaseApiController
{
    public function __construct(Todo2RepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
