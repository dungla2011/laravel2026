<?php

use App\Components\Route2;

Route2::prefix('member')->group(function () {

    Route2::prefix('monitor-setting')->group(function () {
        $route_group_desc = 'Thao tác với MonitorSetting';

        $routeName = 'member.monitor-setting.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\MonitorSettingController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách monitor-setting';

        $routeName = 'member.monitor-setting.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\MonitorSettingController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa monitor-setting';

        $routeName = 'member.monitor-setting.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\MonitorSettingController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo monitor-setting';

        $routeName = 'member.monitor-setting.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\MonitorSettingController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree MonitorSetting';
    });

});
