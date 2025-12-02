<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('order-ship')->group(function () {
        $route_group_desc = 'Thao tác với OrderShip';

        $routeName = 'admin.order-ship.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\OrderShipController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách order-ship';

        $routeName = 'admin.order-ship.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\OrderShipController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa order-ship';

        $routeName = 'admin.order-ship.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\OrderShipController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo order-ship';

        $routeName = 'admin.order-ship.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\OrderShipController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree OrderShip';
    });

});
