<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('conference-cat')->group(function () {
        $route_group_desc = 'Thao tác với ConferenceCat';

        $routeName = 'admin.conference-cat.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\ConferenceCatController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách conference-cat';

        $routeName = 'admin.conference-cat.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\ConferenceCatController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa conference-cat';

        $routeName = 'admin.conference-cat.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\ConferenceCatController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo conference-cat';

        $routeName = 'admin.conference-cat.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\ConferenceCatController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree ConferenceCat';
    });

});
