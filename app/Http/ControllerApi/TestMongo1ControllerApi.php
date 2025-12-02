<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\TestMongo1RepositoryInterface;

class TestMongo1ControllerApi extends BaseApiController
{
    public function __construct(TestMongo1RepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

        parent::__construct();
    }
}
