<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\TreeMngColFixRepositoryInterface;

class TreeMngColFixControllerApi extends BaseApiController
{
    public function __construct(TreeMngColFixRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
