<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {

    }

    /*
    Ví dụ
    class DownloadController extends Controller
    {
        //sẽ load file /resources/views/download/<templateName>/public.blade.php
        public function public($id, $name){
            return $this->getViewLayout();
        }
    }
     */
    public function getViewLayout($default = null, $params = [])
    {
        $layout = getLayoutName();

        $m1 = explode('\\', get_called_class());
        $cls = end($m1);

        $ctlName = strtolower(substr($cls, 0, -10));

        $act = \Illuminate\Support\Facades\Route::getCurrentRoute()->getActionMethod();
        if ($layout) {
            $layout = $layout.'.';
        }

        $view = $ctlName.'.'.$layout.$act;
        if (! $default) {
            $default = $ctlName.'.'.$act;
        }

        if (view()->exists($view)) {
            $viewOK = $view;
        } else {
            $viewOK = $default;
        }

        if (! view()->exists($viewOK)) {

            exit(" <b>  Not found layout: '$viewOK' / '$view' </b>");
        }

//        die('View: '.$viewOK);

        logger('View: '.$viewOK);

//        die('View: '.$viewOK);

        return view($viewOK, $params);
    }
}
