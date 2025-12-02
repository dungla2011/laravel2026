<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\User;
use App\Traits\DeleteModelTrait;

class AdminUserUseApiController extends BaseController
{
    use DeleteModelTrait;

    protected $data;

    public function __construct(User $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

        parent::__construct();

    }
}
