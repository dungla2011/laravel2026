<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Models\Data;
use App\Models\DemoTbl;
use App\Repositories\DemoFolderRepositoryInterface;

class DemoFolderControllerApi extends BaseApiController
{
    //    public function __construct(DemoTbl $data) {
    //        $this->data = $data;
    //    }

    public function __construct(DemoFolderRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

        parent::__construct();
    }
}
