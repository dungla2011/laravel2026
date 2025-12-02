<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('role-user')->group(function () {
        $route_group_desc = 'Thao tác với RoleUser';

        $routeName = 'admin.role-user.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\RoleUserController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách role-user';

        $routeName = 'admin.role-user.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\RoleUserController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa role-user';

        $routeName = 'admin.role-user.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\RoleUserController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo role-user';

        $routeName = 'admin.role-user.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\RoleUserController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree RoleUser';
    });

});
