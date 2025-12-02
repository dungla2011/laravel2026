<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('monitor-config')->group(function () {
        $route_group_desc = 'Thao tác với MonitorConfig';

        $routeName = 'admin.monitor-config.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\MonitorConfigController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách monitor-config';

        $routeName = 'admin.monitor-config.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\MonitorConfigController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa monitor-config';

        $routeName = 'admin.monitor-config.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\MonitorConfigController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo monitor-config';

        $routeName = 'admin.monitor-config.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\MonitorConfigController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree MonitorConfig';
    });

});
