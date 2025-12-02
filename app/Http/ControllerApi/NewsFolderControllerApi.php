<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\NewsFolderRepositoryInterface;

class NewsFolderControllerApi extends BaseApiController
{
    public function __construct(NewsFolderRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
