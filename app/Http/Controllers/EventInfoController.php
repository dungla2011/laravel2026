<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\EventInfo;

class EventInfoController extends BaseController
{
    protected EventInfo $data;

    public function __construct(EventInfo $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
        //Member không cần limit user, vi se limit theo department
        $objPrEx->need_set_uid = 0;
        parent::__construct();
    }

    function qr_scan($eIdAndUser)
    {
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
        echo " <div style='width: 100%; text-align: center; padding: 10px 5px'> Scan QR CODE: <br> <img style='max-width: 80%' src='https://events.dav.edu.vn/images/code_gen/ncbd-event-$eIdAndUser.png'> </div> ";
    }
    function report(){

        return $this->getViewLayout('index.ncbd.event-report');
    }

    function memberEventSummary()
    {
        $isMember = 1;
        return $this->getViewLayout('admin.ncbd.index', compact('isMember'));
    }
    function report_sum(){

        return $this->getViewLayout('index.ncbd.event-report-sum');
    }
    public function userConfirmEvent()
    {

//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r(request()->all());
//        echo "</pre>";
        return $this->getViewLayout('index.ncbd.confirm-email');

    }

    public function tree_index()
    {
        $objMeta = $this->data::getMetaObj();

        return view('admin.default-tree', compact('objMeta'));
    }
}
