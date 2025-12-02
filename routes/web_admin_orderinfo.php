<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('order-info')->group(function () {
        $route_group_desc = 'Thao tác với OrderInfo';

        $routeName = 'admin.order-info.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\OrderInfoController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách order-info';

        $routeName = 'admin.order-info.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\OrderInfoController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa order-info';

        $routeName = 'admin.order-info.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\OrderInfoController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo order-info';

        $routeName = 'admin.order-info.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\OrderInfoController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree OrderInfo';
    });

});
