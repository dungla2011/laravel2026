<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('cloud-server')->group(function () {
        $route_group_desc = 'Thao tác với CloudServer';

        $routeName = 'admin.cloud-server.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\CloudServerController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách cloud-server';

        $routeName = 'admin.cloud-server.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\CloudServerController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa cloud-server';

        $routeName = 'admin.cloud-server.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\CloudServerController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo cloud-server';

        $routeName = 'admin.cloud-server.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\CloudServerController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree CloudServer';
    });

});
