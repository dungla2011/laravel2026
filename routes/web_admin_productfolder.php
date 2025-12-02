<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('product-folder')->group(function () {
        $route_group_desc = 'Thao tác với ProductFolder';

        $routeName = 'admin.product-folder.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\ProductFolderController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách product-folder';

        $routeName = 'admin.product-folder.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\ProductFolderController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa product-folder';

        $routeName = 'admin.product-folder.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\ProductFolderController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo product-folder';

        $routeName = 'admin.product-folder.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\ProductFolderController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree ProductFolder';
    });

});
