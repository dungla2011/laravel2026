<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('plan-define-value')->group(function () {
        $route_group_desc = 'Thao tác với PlanDefineValue';

        $routeName = 'admin.plan-define-value.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\PlanDefineValueController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách plan-define-value';

        $routeName = 'admin.plan-define-value.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\PlanDefineValueController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa plan-define-value';

        $routeName = 'admin.plan-define-value.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\PlanDefineValueController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo plan-define-value';

        $routeName = 'admin.plan-define-value.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\PlanDefineValueController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree PlanDefineValue';
    });

});
