<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('log-user')->group(function () {
        $route_group_desc = 'Thao tác với LogUser';

        $routeName = 'admin.log-user.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\LogUserController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách log-user';

        $routeName = 'admin.log-user.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\LogUserController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa log-user';

        $routeName = 'admin.log-user.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\LogUserController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo log-user';

        $routeName = 'admin.log-user.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\LogUserController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree LogUser';
    });

});
