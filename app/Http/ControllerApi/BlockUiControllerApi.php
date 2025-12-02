<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\BlockUiRepositoryInterface;

class BlockUiControllerApi extends BaseApiController
{
    public function __construct(BlockUiRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
