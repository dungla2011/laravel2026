<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('skus-product-variant-option')->group(function () {
        $route_group_desc = 'Thao tác với SkusProductVariantOption';

        $routeName = 'admin.skus-product-variant-option.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\SkusProductVariantOptionController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách skus-product-variant-option';

        $routeName = 'admin.skus-product-variant-option.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\SkusProductVariantOptionController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa skus-product-variant-option';

        $routeName = 'admin.skus-product-variant-option.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\SkusProductVariantOptionController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo skus-product-variant-option';

        $routeName = 'admin.skus-product-variant-option.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\SkusProductVariantOptionController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree SkusProductVariantOption';
    });

});
