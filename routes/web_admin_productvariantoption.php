<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('product-variant-option')->group(function () {
        $route_group_desc = 'Thao tác với ProductVariantOption';

        $routeName = 'admin.product-variant-option.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\ProductVariantOptionController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách product-variant-option';

        $routeName = 'admin.product-variant-option.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\ProductVariantOptionController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa product-variant-option';

        $routeName = 'admin.product-variant-option.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\ProductVariantOptionController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo product-variant-option';

        $routeName = 'admin.product-variant-option.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\ProductVariantOptionController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree ProductVariantOption';
    });

});
