<?php

use App\Components\Route2;

Route2::prefix('member')->group(function () {

    Route2::prefix('notification')->group(function () {
        $route_group_desc = 'Thao tác với Notification';

        $routeName = 'member.notification.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\NotificationController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách notification';

        $routeName = 'member.notification.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\NotificationController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa notification';

        $routeName = 'member.notification.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\NotificationController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo notification';

        $routeName = 'member.notification.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\NotificationController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree Notification';
    });

});
