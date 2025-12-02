<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\QuizFolderRepositoryInterface;

class QuizFolderControllerApi extends BaseApiController
{
    public function __construct(QuizFolderRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
