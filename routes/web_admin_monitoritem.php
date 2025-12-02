<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('monitor-item')->group(function () {
        $route_group_desc = 'Thao tác với MonitorItem';

        $routeName = 'admin.monitor-item.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\MonitorItemController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách monitor-item';

        $routeName = 'admin.monitor-item.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\MonitorItemController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa monitor-item';

        $routeName = 'admin.monitor-item.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\MonitorItemController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo monitor-item';

        $routeName = 'admin.monitor-item.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\MonitorItemController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree MonitorItem';
    });

});
