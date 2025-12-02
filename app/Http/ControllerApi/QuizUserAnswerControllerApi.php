<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\QuizUserAnswerRepositoryInterface;

class QuizUserAnswerControllerApi extends BaseApiController
{
    public function __construct(QuizUserAnswerRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
