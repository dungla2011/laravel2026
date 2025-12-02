<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\MediaCatRepositoryInterface;

class MediaCatControllerApi extends BaseApiController
{
    public function __construct(MediaCatRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

        parent::__construct();
    }
}
