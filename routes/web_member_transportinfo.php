<?php

use App\Components\Route2;

Route2::prefix('member')->group(function () {

    Route2::prefix('transport-info')->group(function () {
        $route_group_desc = 'Thao tác với TransportInfo';

        $routeName = 'member.transport-info.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\TransportInfoController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách transport-info';

        $routeName = 'member.transport-info.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\TransportInfoController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa transport-info';

        $routeName = 'member.transport-info.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\TransportInfoController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo transport-info';

        $routeName = 'member.transport-info.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\TransportInfoController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree TransportInfo';
    });

});
