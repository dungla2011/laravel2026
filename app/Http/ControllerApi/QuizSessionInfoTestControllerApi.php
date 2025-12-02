<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\QuizSessionInfoTestRepositoryInterface;

class QuizSessionInfoTestControllerApi extends BaseApiController
{
    public function __construct(QuizSessionInfoTestRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
