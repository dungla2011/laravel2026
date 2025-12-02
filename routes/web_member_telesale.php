<?php

use App\Components\Route2;

Route2::prefix('member')->group(function () {

    Route2::prefix('telesale')->group(function () {
        $route_group_desc = 'Thao tác với Telesale';

        $routeName = 'member.telesale.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\TelesaleController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách telesale';

        $routeName = 'member.telesale.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\TelesaleController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa telesale';

        $routeName = 'member.telesale.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\TelesaleController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo telesale';

        $routeName = 'member.telesale.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\TelesaleController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree Telesale';
    });

});
