<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('tag')->group(function () {
        $route_group_desc = 'Thao tác với Tag';

        $routeName = 'admin.tags.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\TagController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách demo';

        $routeName = 'admin.tags.edit1';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\TagController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa demo';

        $routeName = 'admin.tags.create2';
        $r = Route2::get('/create',
            [\App\Http\Controllers\TagController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo demo';

        $routeName = 'admin.tags.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\TagController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree Demo';
    });

});
