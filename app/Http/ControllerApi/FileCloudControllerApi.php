<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\FileCloudRepositoryInterface;

class FileCloudControllerApi extends BaseApiController
{
    public function __construct(FileCloudRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

    }
}
