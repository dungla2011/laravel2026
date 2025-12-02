<?php

use App\Components\Route2;

Route2::prefix('member')->group(function () {

    Route2::prefix('demo-mg')->group(function () {
        $route_group_desc = 'Thao tác với Demo';

        $routeName = 'member.demo-mg.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\DemoMgController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách demo';

        $routeName = 'member.demo-mg.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\DemoMgController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa demo';

        $routeName = 'member.demo-mg.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\DemoMgController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo demo';

        $routeName = 'member.demo-mg.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\DemoMgController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree Demo';
    });

});
