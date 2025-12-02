<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\QuizUserAndTestRepositoryInterface;

class QuizUserAndTestControllerApi extends BaseApiController
{
    public function __construct(QuizUserAndTestRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
