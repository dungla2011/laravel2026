<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('user-recharge')->group(function () {
        $route_group_desc = 'Thao tác với UserRecharge';

        $routeName = 'admin.user-recharge.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\UserRechargeController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách user-recharge';

        $routeName = 'admin.user-recharge.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\UserRechargeController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa user-recharge';

        $routeName = 'admin.user-recharge.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\UserRechargeController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo user-recharge';

        $routeName = 'admin.user-recharge.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\UserRechargeController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree UserRecharge';
    });

});
