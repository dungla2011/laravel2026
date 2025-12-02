<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\TypingTestResultRepositoryInterface;

class TypingTestResultControllerApi extends BaseApiController
{
    public function __construct(TypingTestResultRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
