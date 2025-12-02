<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('conference-info')->group(function () {
        $route_group_desc = 'Thao tác với ConferenceInfo';

        $routeName = 'admin.conference-info.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\ConferenceInfoController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách conference-info';

        $routeName = 'admin.conference-info.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\ConferenceInfoController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa conference-info';

        $routeName = 'admin.conference-info.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\ConferenceInfoController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo conference-info';

        $routeName = 'admin.conference-info.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\ConferenceInfoController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree ConferenceInfo';
    });

});
