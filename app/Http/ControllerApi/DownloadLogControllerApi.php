<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\DownloadLogRepositoryInterface;

class DownloadLogControllerApi extends BaseApiController
{
    public function __construct(DownloadLogRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
