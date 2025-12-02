<?php

use App\Components\Route2;

Route2::prefix('member')->group(function () {

    Route2::prefix('share-with-me')->group(function () {
        $route_group_desc = 'Thao tác với FileSharePermission';

        $routeName = 'member.file-share-permission.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\FileSharePermissionController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách file-share-permission';

        $routeName = 'member.file-share-permission.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\FileSharePermissionController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa file-share-permission';

        $routeName = 'member.file-share-permission.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\FileSharePermissionController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo file-share-permission';

        $routeName = 'member.file-share-permission.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\FileSharePermissionController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree FileSharePermission';
    });

});
