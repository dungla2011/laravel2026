<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\AffiliateLogRepositoryInterface;

class AffiliateLogControllerApi extends BaseApiController
{
    public function __construct(AffiliateLogRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

        parent::__construct();
    }
}
