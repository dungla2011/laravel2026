<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\MediaFolderRepositoryInterface;

class MediaFolderControllerApi extends BaseApiController
{
    public function __construct(MediaFolderRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }
}
