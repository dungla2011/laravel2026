<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\MediaLinkRepositoryInterface;

class MediaLinkControllerApi extends BaseApiController
{
    public function __construct(MediaLinkRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
