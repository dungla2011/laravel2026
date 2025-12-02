<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('product-variant')->group(function () {
        $route_group_desc = 'Thao tác với ProductVariant';

        $routeName = 'admin.product-variant.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\ProductVariantController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách product-variant';

        $routeName = 'admin.product-variant.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\ProductVariantController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa product-variant';

        $routeName = 'admin.product-variant.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\ProductVariantController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo product-variant';

        $routeName = 'admin.product-variant.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\ProductVariantController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree ProductVariant';
    });

});
