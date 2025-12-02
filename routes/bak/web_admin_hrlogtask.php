<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('hr-log-task')->group(function () {
        $route_group_desc = 'Thao tác với HrLogTask';

        $routeName = 'admin.hr-log-task.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\HrLogTaskController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách hr-log-task';

        $routeName = 'admin.hr-log-task.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\HrLogTaskController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa hr-log-task';

        $routeName = 'admin.hr-log-task.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\HrLogTaskController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo hr-log-task';

        $routeName = 'admin.hr-log-task.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\HrLogTaskController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree HrLogTask';
    });

});
