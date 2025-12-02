<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('block-ui')->group(function () {
        $route_group_desc = 'Thao tác với BlockUi';

        $routeName = 'admin.block-ui.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\BlockUiController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách block-ui';

        $routeName = 'admin.block-ui.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\BlockUiController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa block-ui';

        $routeName = 'admin.block-ui.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\BlockUiController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo block-ui';

        $routeName = 'admin.block-ui.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\BlockUiController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree BlockUi';
    });

});
