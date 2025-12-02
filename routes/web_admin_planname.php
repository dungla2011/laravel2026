<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('plan-name')->group(function () {
        $route_group_desc = 'Thao tác với PlanName';

        $routeName = 'admin.plan-name.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\PlanNameController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách plan-name';

        $routeName = 'admin.plan-name.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\PlanNameController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa plan-name';

        $routeName = 'admin.plan-name.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\PlanNameController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo plan-name';

        $routeName = 'admin.plan-name.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\PlanNameController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree PlanName';
    });

});
