<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\TypingLessonRepositoryInterface;

class TypingLessonControllerApi extends BaseApiController
{
    public function __construct(TypingLessonRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
