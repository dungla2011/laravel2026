<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('change-log')->group(function () {
        $route_group_desc = 'Thao tác với ChangeLog';

        $routeName = 'admin.change-log.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\ChangeLogController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách change-log';

        $routeName = 'admin.change-log.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\ChangeLogController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa change-log';

        $routeName = 'admin.change-log.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\ChangeLogController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo change-log';

        $routeName = 'admin.change-log.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\ChangeLogController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree ChangeLog';
    });

});
