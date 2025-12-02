<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\FileCloud;

class FileCloudController extends BaseController
{
    protected FileCloud $data;

    public function __construct(FileCloud $data, clsParamRequestEx $objPrEx)
    {

        $this->data = $data;
        $this->objParamEx = $objPrEx;

    }
}
