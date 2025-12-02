<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\Notification;

class NotificationController extends BaseController
{
    protected Notification $data;

    public function __construct(Notification $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }

    public function tree_index()
    {
        $objMeta = $this->data::getMetaObj();

        return view('admin.default-tree', compact('objMeta'));
    }
}
