<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('event-face-info')->group(function () {
        $route_group_desc = 'Thao tác với EventFaceInfo';

        $routeName = 'admin.event-face-info.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\EventFaceInfoController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách event-face-info';

        $routeName = 'admin.event-face-info.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\EventFaceInfoController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa event-face-info';

        $routeName = 'admin.event-face-info.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\EventFaceInfoController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo event-face-info';

        $routeName = 'admin.event-face-info.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\EventFaceInfoController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree EventFaceInfo';
    });

});
