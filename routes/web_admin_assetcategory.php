<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('asset-category')->group(function () {
        $route_group_desc = 'Thao tác với AssetCategory';

        $routeName = 'admin.asset-category.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\AssetCategoryController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách asset-category';

        $routeName = 'admin.asset-category.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\AssetCategoryController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa asset-category';

        $routeName = 'admin.asset-category.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\AssetCategoryController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo asset-category';

        $routeName = 'admin.asset-category.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\AssetCategoryController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree AssetCategory';
    });

});
