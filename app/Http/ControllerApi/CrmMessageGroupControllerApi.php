<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\CrmMessageGroupRepositoryInterface;

class CrmMessageGroupControllerApi extends BaseApiController
{
    public function __construct(CrmMessageGroupRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

        parent::__construct();
    }
}
