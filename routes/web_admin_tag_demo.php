<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('tag-demo')->group(function () {
        $route_group_desc = 'Thao tác với Tag';

        $routeName = 'admin.tags-demo.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\TagDemoController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách demo';

        $routeName = 'admin.tags.edit2';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\TagDemoController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa demo';

        $routeName = 'admin.tags.create3';
        $r = Route2::get('/create',
            [\App\Http\Controllers\TagDemoController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo demo';

        $routeName = 'admin.tags.tree2';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\TagDemoController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree Demo';
    });

});
