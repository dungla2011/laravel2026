<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('product-attribute')->group(function () {
        $route_group_desc = 'Thao tác với ProductAttribute';

        $routeName = 'admin.product-attribute.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\ProductAttributeController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách product-attribute';

        $routeName = 'admin.product-attribute.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\ProductAttributeController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa product-attribute';

        $routeName = 'admin.product-attribute.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\ProductAttributeController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo product-attribute';

        $routeName = 'admin.product-attribute.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\ProductAttributeController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree ProductAttribute';
    });

});
