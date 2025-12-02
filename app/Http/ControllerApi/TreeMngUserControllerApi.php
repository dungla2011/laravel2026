<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\TreeMngUserRepositoryInterface;

class TreeMngUserControllerApi extends BaseApiController
{
    public function __construct(TreeMngUserRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
