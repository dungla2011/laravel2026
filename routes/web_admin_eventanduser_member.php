<?php

use App\Components\Route2;

Route2::prefix('member')->group(function () {

    Route2::prefix('event-and-user')->group(function () {
        $route_group_desc = 'Thao tác với EventAndUser';

        $routeName = 'member.event-and-user.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\EventAndUserController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách event-and-user';

        $routeName = 'member.event-and-user.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\EventAndUserController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa event-and-user';

        $routeName = 'member.event-and-user.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\EventAndUserController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo event-and-user';

        $routeName = 'member.event-and-user.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\EventAndUserController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree EventAndUser';
    });

});
