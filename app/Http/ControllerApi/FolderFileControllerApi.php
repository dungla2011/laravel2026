<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\FolderFileRepositoryInterface;

class FolderFileControllerApi extends BaseApiController
{
    public function __construct(FolderFileRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
