<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('event-user-group')->group(function () {
        $route_group_desc = 'Thao tác với EventUserGroup';

        $routeName = 'admin.event-user-group.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\EventUserGroupController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách event-user-group';

        $routeName = 'admin.event-user-group.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\EventUserGroupController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa event-user-group';

        $routeName = 'admin.event-user-group.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\EventUserGroupController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo event-user-group';

        $routeName = 'admin.event-user-group.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\EventUserGroupController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree EventUserGroup';
    });

});
