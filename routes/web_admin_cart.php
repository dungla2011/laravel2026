<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('cart')->group(function () {
        $route_group_desc = 'Thao tác với Cart';

        $routeName = 'admin.cart.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\CartController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách cart';

        $routeName = 'admin.cart.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\CartController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa cart';

        $routeName = 'admin.cart.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\CartController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo cart';

        $routeName = 'admin.cart.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\CartController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree Cart';
    });

});
