<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\MyDocumentCatRepositoryInterface;

class MyDocumentCatControllerApi extends BaseApiController
{
    public function __construct(MyDocumentCatRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
