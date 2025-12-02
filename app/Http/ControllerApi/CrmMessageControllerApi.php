<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\CrmMessageRepositoryInterface;

class CrmMessageControllerApi extends BaseApiController
{
    public function __construct(CrmMessageRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

        parent::__construct();
    }
}
