<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('site-mng')->group(function () {
        $route_group_desc = 'Thao tác với SiteMng';

        $routeName = 'admin.site-mng.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\SiteMngController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách site-mng';

        $routeName = 'admin.site-mng.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\SiteMngController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa site-mng';

        $routeName = 'admin.site-mng.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\SiteMngController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo site-mng';

        $routeName = 'admin.site-mng.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\SiteMngController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree SiteMng';
    });

});
