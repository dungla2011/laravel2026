<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('product')->group(function () {
        $route_group_desc = 'Thao tác với Product';

        $routeName = 'admin.product.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\ProductController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách product';

        $routeName = 'admin.product.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\ProductController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa product';

        $routeName = 'admin.product.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\ProductController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo product';

        $routeName = 'admin.product.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\ProductController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree Product';
    });

});
