<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\QuizClassRepositoryInterface;

class QuizClassControllerApi extends BaseApiController
{
    public function __construct(QuizClassRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
