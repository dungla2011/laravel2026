<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('file-share-permission')->group(function () {
        $route_group_desc = 'Thao tác với FileSharePermission';

        $routeName = 'admin.file-share-permission.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\FileSharePermissionController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách file-share-permission';

        $routeName = 'admin.file-share-permission.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\FileSharePermissionController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa file-share-permission';

        $routeName = 'admin.file-share-permission.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\FileSharePermissionController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo file-share-permission';

        $routeName = 'admin.file-share-permission.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\FileSharePermissionController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree FileSharePermission';
    });

});
