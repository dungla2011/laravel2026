<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('plan-define')->group(function () {
        $route_group_desc = 'Thao tác với PlanDefine';

        $routeName = 'admin.plan-define.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\PlanDefineController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách plan-define';

        $routeName = 'admin.plan-define.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\PlanDefineController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa plan-define';

        $routeName = 'admin.plan-define.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\PlanDefineController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo plan-define';

        $routeName = 'admin.plan-define.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\PlanDefineController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree PlanDefine';
    });

});
