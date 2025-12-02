<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\QuizTestQuestionRepositoryInterface;

class QuizTestQuestionControllerApi extends BaseApiController
{
    public function __construct(QuizTestQuestionRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
