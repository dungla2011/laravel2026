<?php

use App\Components\Route2;

Route2::prefix('member')->group(function () {

    Route2::prefix('event-send-info-log')->group(function () {
        $route_group_desc = 'Thao tác với EventSendInfoLog';

        $routeName = 'member.event-send-info-log.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\EventSendInfoLogController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách event-send-info-log';

        $routeName = 'member.event-send-info-log.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\EventSendInfoLogController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa event-send-info-log';

        $routeName = 'member.event-send-info-log.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\EventSendInfoLogController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo event-send-info-log';

        $routeName = 'member.event-send-info-log.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\EventSendInfoLogController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree EventSendInfoLog';
    });

});
