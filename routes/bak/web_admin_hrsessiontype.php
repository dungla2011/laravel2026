<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('hr-session-type')->group(function () {
        $route_group_desc = 'Thao tác với HrSessionType';

        $routeName = 'admin.hr-session-type.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\HrSessionTypeController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách hr-session-type';

        $routeName = 'admin.hr-session-type.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\HrSessionTypeController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa hr-session-type';

        $routeName = 'admin.hr-session-type.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\HrSessionTypeController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo hr-session-type';

        $routeName = 'admin.hr-session-type.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\HrSessionTypeController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree HrSessionType';
    });

});
