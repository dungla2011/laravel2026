<?php

use App\Components\Route2;

Route2::prefix('member')->group(function () {

    Route2::prefix('event-send-action')->group(function () {
        $route_group_desc = 'Thao tác với EventSendAction';

        $routeName = 'member.event-send-action.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\EventSendActionController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách event-send-action';

        $routeName = 'member.event-send-action.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\EventSendActionController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa event-send-action';

        $routeName = 'member.event-send-action.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\EventSendActionController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo event-send-action';

        $routeName = 'member.event-send-action.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\EventSendActionController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree EventSendAction';
    });

});
