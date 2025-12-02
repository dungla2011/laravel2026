<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('sku')->group(function () {
        $route_group_desc = 'Thao tác với Sku';

        $routeName = 'admin.sku.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\SkuController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách sku';

        $routeName = 'admin.sku.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\SkuController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa sku';

        $routeName = 'admin.sku.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\SkuController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo sku';

        $routeName = 'admin.sku.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\SkuController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree Sku';
    });

});
