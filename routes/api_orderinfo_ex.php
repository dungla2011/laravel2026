<?php

use App\Components\Route2;

Route2::prefix('/order-info')->group(function () {

    $route_group_desc = 'API - Thao tác với OrderInfo';
    $nameModule = 'order-info';
    $modelUsing_ = \App\Models\OrderInfo::class;

    $cls = \App\Http\ControllerApi\OrderInfoControllerApi::class;

    $routeName = 'api.'.$nameModule.'.send_to_ghtk';
    $r = Route2::match(['GET', 'POST'], '/send_to_ghtk', [
        \App\Http\ControllerApi\OrderInfoControllerApi::class, 'sendToGhtk',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Gửi đơn tới ghtk';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.getHtmlPrintOrder';
    $r = Route2::match(['GET', 'POST'], '/getHtmlPrintOrder', [
        \App\Http\ControllerApi\OrderInfoControllerApi::class, 'getHtmlPrintOrder',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Get Html để in';
    $r->modelUsing_ = $modelUsing_;

});
