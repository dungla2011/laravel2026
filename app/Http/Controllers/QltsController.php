<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Components\Recusive;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;

class QltsController extends BaseController
{
    function scanQr()
    {
        return view('qlts.scanQr');
    }
}
