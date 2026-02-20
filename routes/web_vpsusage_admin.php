<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('vps-usage')->group(function () {
        $route_group_desc = 'Thao tác với VpsUsage';

        $routeName = 'admin.vps-usage.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\VpsUsageController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách vps-usage';

        $routeName = 'admin.vps-usage.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\VpsUsageController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa vps-usage';

        $routeName = 'admin.vps-usage.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\VpsUsageController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo vps-usage';

        $routeName = 'admin.vps-usage.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\VpsUsageController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree VpsUsage';
    });

});
