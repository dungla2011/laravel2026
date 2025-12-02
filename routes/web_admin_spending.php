<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('spending')->group(function () {
        $route_group_desc = 'Thao tác với Spending';

        $routeName = 'admin.spending.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\SpendingController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách spending';

        $routeName = 'admin.spending.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\SpendingController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa spending';

        $routeName = 'admin.spending.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\SpendingController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo spending';

        $routeName = 'admin.spending.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\SpendingController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree Spending';
    });

});
