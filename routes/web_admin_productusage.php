<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('product-usage')->group(function () {
        $route_group_desc = 'Thao tác với ProductUsage';

        $routeName = 'admin.product-usage.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\ProductUsageController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách product-usage';

        $routeName = 'admin.product-usage.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\ProductUsageController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa product-usage';

        $routeName = 'admin.product-usage.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\ProductUsageController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo product-usage';

        $routeName = 'admin.product-usage.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\ProductUsageController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree ProductUsage';
    });

});
