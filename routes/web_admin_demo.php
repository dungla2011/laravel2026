<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('demo-api')->group(function () {
        $route_group_desc = 'Thao tác với Demo';

        $routeName = 'admin.demo-api.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\DemoUseApiController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách demo';

        $routeName = 'admin.demo-api.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\DemoUseApiController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa demo';

        $routeName = 'admin.demo-api.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\DemoUseApiController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo demo';

        $routeName = 'admin.demo-api.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\DemoUseApiController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree Demo';
    });

});
