<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('order-item')->group(function () {
        $route_group_desc = 'Thao tác với OrderItem';

        $routeName = 'admin.order-item.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\OrderItemController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách order-item';

        $routeName = 'admin.order-item.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\OrderItemController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa order-item';

        $routeName = 'admin.order-item.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\OrderItemController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo order-item';

        $routeName = 'admin.order-item.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\OrderItemController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree OrderItem';
    });

});


Route2::prefix('member')->group(function () {

    Route2::prefix('order-item')->group(function () {
        $route_group_desc = 'Thao tác với OrderItem';

        $routeName = 'member.order-item.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\OrderItemController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách order-item';

        $routeName = 'member.order-item.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\OrderItemController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa order-item';

        $routeName = 'member.order-item.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\OrderItemController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo order-item';

        $routeName = 'member.order-item.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\OrderItemController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree OrderItem';
    });

});
