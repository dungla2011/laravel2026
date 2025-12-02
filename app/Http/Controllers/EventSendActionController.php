<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\EventSendAction;

class EventSendActionController extends BaseController
{
    protected EventSendAction $data;

    public function __construct(EventSendAction $data, clsParamRequestEx $objPrEx)
    {
        //Member không cần limit user, vi se limit theo department
        $objPrEx->need_set_uid = 0;
        $this->data = $data;
        $this->objParamEx = $objPrEx;
        parent::__construct();
    }

    public function tree_index()
    {
        $objMeta = $this->data::getMetaObj();

        return view('admin.default-tree', compact('objMeta'));
    }
}
