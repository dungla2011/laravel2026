<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\CrmMessagesRepositoryInterface;

class CrmMessagesControllerApi extends BaseApiController
{
    public function __construct(CrmMessagesRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

        parent::__construct();
    }
}
