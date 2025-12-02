<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\FileSharePermissionRepositoryInterface;

class FileSharePermissionControllerApi extends BaseApiController
{
    public function __construct(FileSharePermissionRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

        parent::__construct();
    }
}
