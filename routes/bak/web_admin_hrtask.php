<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('hr-task')->group(function () {
        $route_group_desc = 'Thao tác với HrTask';

        $routeName = 'admin.hr-task.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\HrTaskController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách hr-task';

        $routeName = 'admin.hr-task.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\HrTaskController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa hr-task';

        $routeName = 'admin.hr-task.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\HrTaskController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo hr-task';

        $routeName = 'admin.hr-task.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\HrTaskController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree HrTask';
    });

});
