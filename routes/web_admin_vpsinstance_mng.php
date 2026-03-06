<?php

use App\Components\Route2;

Route2::prefix('manager')->group(function () {

    Route2::prefix('vps-instance')->group(function () {
        $route_group_desc = 'Thao tác với VpsInstance';

        $routeName = 'mng.vps-instance.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\VpsInstanceController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách vps-instance';

        $routeName = 'mng.vps-instance.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\VpsInstanceController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa vps-instance';

        $routeName = 'mng.vps-instance.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\VpsInstanceController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo vps-instance';

        $routeName = 'mng.vps-instance.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\VpsInstanceController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree VpsInstance';
    });

});

