<?php

use App\Components\Route2;

Route::get('/qr/{id}',
    [\App\Http\Controllers\EventInfoController::class, 'qr_scan'])
    ->name('qr_event');

Route2::prefix('admin')->group(function () {

    Route2::prefix('event-user-info')->group(function () {
        $route_group_desc = 'Thao tác với EventUserInfo';

        $routeName = 'admin.event-user-info.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\EventUserInfoController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách event-user-info';

        $routeName = 'admin.event-user-info.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\EventUserInfoController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa event-user-info';

        $routeName = 'admin.event-user-info.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\EventUserInfoController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo event-user-info';

        $routeName = 'admin.event-user-info.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\EventUserInfoController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree EventUserInfo';
    });

});
