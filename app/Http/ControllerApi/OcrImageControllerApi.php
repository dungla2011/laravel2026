<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\OcrImageRepositoryInterface;

class OcrImageControllerApi extends BaseApiController
{
    public function __construct(OcrImageRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
