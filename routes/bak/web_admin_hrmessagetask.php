<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('hr-message-task')->group(function () {
        $route_group_desc = 'Thao tác với HrMessageTask';

        $routeName = 'admin.hr-message-task.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\HrMessageTaskController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách hr-message-task';

        $routeName = 'admin.hr-message-task.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\HrMessageTaskController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa hr-message-task';

        $routeName = 'admin.hr-message-task.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\HrMessageTaskController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo hr-message-task';

        $routeName = 'admin.hr-message-task.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\HrMessageTaskController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree HrMessageTask';
    });

});
