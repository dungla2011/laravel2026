<?php

use App\Components\Route2;

use \App\Http\ControllerApi\Api4sV1Controller;

Route2::prefix('/v1')->group(function () {

    $route_group_desc = 'API - 4sV1';
    $nameModule = '4sAPIV1';
    $modelUsing_ = null;

    $routeName = 'api.'.$nameModule.'.ApiV10';
    $r = Route2::match(['get', 'post'], '', [
//    $r = Route2::post('/', [
        Api4sV1Controller::class, 'ApiV1',
    ])->name($routeName);
    if ($r instanceof Route2);
//    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'GetLinkDl';
    $r->modelUsing_ = $modelUsing_;
    ///////////////////////////////////////////////////////////

});

