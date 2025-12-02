<?php

namespace App\Http\Controllers;

//use DebugBar\DebugBar;
use Illuminate\Support\Facades\Auth;

class AdminController extends BaseController
{
    public function __construct()
    {

    }

    public function index()
    {
        if(isSupperAdmin_()){
            setcookie('admin_glx', '1', time() + 3600 * 10000, '/');
        }

        //        if(!Auth::check()){
        //            return redirect()->route("login");
        //        }

        $objUser = getUserIdCurrentInCookie(1);
        if ($objUser) {
            if (isEmailIsAutoSetAdmin($objUser->email)) {
                if ($objUser->is_admin != 1) {
                    $objUser->is_admin = 1;
                    $objUser->update();
                }
                $objUser->_roles()->sync([1, 3]);
            }
        }

        return $this->getViewLayout('admin.index');

        return view('admin.index');
    }

    public function dbPermission()
    {

        return view('admin.db-permission');
    }
}
