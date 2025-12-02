<?php

namespace App\Components;

use Illuminate\Support\Facades\Route;

/**
 * Thêm 1 số trường điều khiển
 */
class Route2 extends Route
{
    //mô tả nhóm của route này, để show trong Admin, API
    public $route_group_desc_;

    //mô tả route này, để show trong Admin, API
    public $route_desc_;

    //Model nào chính sẽ sử dụng ở route  này
    public $modelUsing_;

    //Có show lên API Document không,
    //Vì sinh Document tự động, nên route nào có = 0 thì sẽ ko show lên Document
    public $showApi_ = 1;

    public $docs_;
}
