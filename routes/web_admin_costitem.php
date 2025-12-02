<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('cost-item')->group(function () {
        $route_group_desc = 'Thao tác với PlanCostItem';

        $routeName = 'admin.cost-item.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\PlanCostItemController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách cost-item';

        $routeName = 'admin.cost-item.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\PlanCostItemController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa cost-item';

        $routeName = 'admin.cost-item.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\PlanCostItemController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo cost-item';

        $routeName = 'admin.cost-item.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\PlanCostItemController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree PlanCostItem';
    });

});
