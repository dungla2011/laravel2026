<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('department-event')->group(function () {
        $route_group_desc = 'Thao tác với DepartmentEvent';

        $routeName = 'admin.department-event.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\DepartmentEventController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách department-event';

        $routeName = 'admin.department-event.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\DepartmentEventController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa department-event';

        $routeName = 'admin.department-event.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\DepartmentEventController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo department-event';

        $routeName = 'admin.department-event.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\DepartmentEventController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree DepartmentEvent';
    });

});
