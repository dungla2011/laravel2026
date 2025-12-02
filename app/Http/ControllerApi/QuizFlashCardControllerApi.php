<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\QuizFlashCardRepositoryInterface;

class QuizFlashCardControllerApi extends BaseApiController
{
    public function __construct(QuizFlashCardRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
