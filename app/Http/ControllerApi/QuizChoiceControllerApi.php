<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\QuizChoiceRepositoryInterface;

class QuizChoiceControllerApi extends BaseApiController
{
    public function __construct(QuizChoiceRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
