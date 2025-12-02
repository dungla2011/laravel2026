<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('hr-sample-time-event')->group(function () {
        $route_group_desc = 'Thao tác với HrSampleTimeEvent';

        $routeName = 'admin.hr-sample-time-event.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\HrSampleTimeEventController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách hr-sample-time-event';

        $routeName = 'admin.hr-sample-time-event.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\HrSampleTimeEventController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa hr-sample-time-event';

        $routeName = 'admin.hr-sample-time-event.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\HrSampleTimeEventController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo hr-sample-time-event';

        $routeName = 'admin.hr-sample-time-event.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\HrSampleTimeEventController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree HrSampleTimeEvent';
    });

});
