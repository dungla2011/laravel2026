<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('department-user')->group(function () {
        $route_group_desc = 'Thao tác với DepartmentUser';

        $routeName = 'admin.department-user.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\DepartmentUserController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách department-user';

        $routeName = 'admin.department-user.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\DepartmentUserController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa department-user';

        $routeName = 'admin.department-user.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\DepartmentUserController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo department-user';

        $routeName = 'admin.department-user.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\DepartmentUserController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree DepartmentUser';
    });

});
