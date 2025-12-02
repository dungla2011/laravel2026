<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\MediaAuthorRepositoryInterface;

class MediaAuthorControllerApi extends BaseApiController
{
    public function __construct(MediaAuthorRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

        parent::__construct();
    }
}
