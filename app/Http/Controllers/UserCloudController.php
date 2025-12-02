<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\UserCloud;

class UserCloudController extends BaseController
{
    protected UserCloud $data;

    public function __construct(UserCloud $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
        parent::__construct();
    }
}
