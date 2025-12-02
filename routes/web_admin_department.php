<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('department')->group(function () {
        $route_group_desc = 'Thao tác với Department';

        $routeName = 'admin.department.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\DepartmentController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách department';

        $routeName = 'admin.department.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\DepartmentController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa department';

        $routeName = 'admin.department.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\DepartmentController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo department';

        $routeName = 'admin.department.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\DepartmentController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree Department';
    });

});
