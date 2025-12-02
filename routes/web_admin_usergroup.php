<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('user-group')->group(function () {
        $route_group_desc = 'Thao tác với UserGroup';

        $routeName = 'admin.user-group.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\UserGroupController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách user-group';

        $routeName = 'admin.user-group.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\UserGroupController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa user-group';

        $routeName = 'admin.user-group.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\UserGroupController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo user-group';

        $routeName = 'admin.user-group.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\UserGroupController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree UserGroup';
    });

});
