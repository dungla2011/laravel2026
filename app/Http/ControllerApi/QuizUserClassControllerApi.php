<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\QuizUserClassRepositoryInterface;

class QuizUserClassControllerApi extends BaseApiController
{
    public function __construct(QuizUserClassRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
