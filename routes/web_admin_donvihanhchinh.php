<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('don-vi-hanh-chinh')->group(function () {
        $route_group_desc = 'Thao tác với DonViHanhChinh';

        $routeName = 'admin.don-vi-hanh-chinh.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\DonViHanhChinhController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách don-vi-hanh-chinh';

        $routeName = 'admin.don-vi-hanh-chinh.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\DonViHanhChinhController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa don-vi-hanh-chinh';

        $routeName = 'admin.don-vi-hanh-chinh.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\DonViHanhChinhController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo don-vi-hanh-chinh';

        $routeName = 'admin.don-vi-hanh-chinh.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\DonViHanhChinhController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree DonViHanhChinh';
    });

});
