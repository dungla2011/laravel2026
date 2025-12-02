<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Models\Data;
use App\Models\DemoTbl;
use App\Repositories\MenuTreeRepositoryInterface;

class MenuTreeControllerApi extends BaseApiController
{
    //    public function __construct(DemoTbl $data) {
    //        $this->data = $data;
    //    }

    public function __construct(MenuTreeRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

        $this->objParamEx->set_gid = 1;

    }
}
