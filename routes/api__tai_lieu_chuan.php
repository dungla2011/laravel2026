<?php

use App\Components\Route2;

use \App\Http\ControllerApi\Api4sV1Controller;

Route2::prefix('/tai-lieu')->group(function () {

    $route_group_desc = 'API - 4sV1';
    $nameModule = 'tai-lieu';
    $modelUsing_ = null;

    $routeName = 'api.'.$nameModule.'.getLinkDownloadDoc';
    $r = Route2::match(['get', 'post'], '/getLinkDownloadDoc', [
        \App\Http\ControllerApi\DownloadFileControllerApi::class, 'getLinkDownloadDoc',
    ])->name($routeName);
    if ($r instanceof Route2);
//    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'getLinkDownloadDoc';
    $r->modelUsing_ = $modelUsing_;
    ///////////////////////////////////////////////////////////



});

