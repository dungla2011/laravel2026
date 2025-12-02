<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\EventAndUser;

class EventAndUserController extends BaseController
{
    protected EventAndUser $data;

    public function __construct(EventAndUser $data, clsParamRequestEx $objPrEx)
    {
        //Member không cần limit user, vi se limit theo department
        $objPrEx->need_set_uid = 0;
        $this->data = $data;
//        $objPrEx->need_set_uid = 0;
        $this->objParamEx = $objPrEx;
        parent::__construct();
    }

    public function tree_index()
    {
        $objMeta = $this->data::getMetaObj();

        return view('admin.default-tree', compact('objMeta'));
    }
}
