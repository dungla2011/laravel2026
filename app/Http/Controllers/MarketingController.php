<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MarketingController extends Controller
{
    public function lien_ket_mar_shb(Request $request)
    {
        return $this->getViewLayout();
    }

    public function lien_ket_mar_shb_cookie(Request $request)
    {
        return $this->getViewLayout();
    }

    public function lien_ket_mar_abc(Request $request)
    {
        return $this->getViewLayout();
    }

    public function lien_ket_mar_abc_cookie(Request $request)
    {
        return $this->getViewLayout();
    }
}
