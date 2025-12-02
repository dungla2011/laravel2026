<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\UploaderInfoRepositoryInterface;

class UploaderInfoControllerApi extends BaseApiController
{
    public function __construct(UploaderInfoRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

        parent::__construct();
    }
}
