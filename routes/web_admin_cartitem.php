<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('cart-item')->group(function () {
        $route_group_desc = 'Thao tác với CartItem';

        $routeName = 'admin.cart-item.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\CartItemController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách cart-item';

        $routeName = 'admin.cart-item.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\CartItemController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa cart-item';

        $routeName = 'admin.cart-item.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\CartItemController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo cart-item';

        $routeName = 'admin.cart-item.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\CartItemController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree CartItem';
    });

});
