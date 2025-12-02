<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('task-info')->group(function () {
        $route_group_desc = 'Thao tác với TaskInfo';

        $routeName = 'admin.task-info.index1';
        $r = Route2::get('/', [
            \App\Http\Controllers\TaskInfoController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách task-info';

        $routeName = 'admin.task-info.edit2';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\TaskInfoController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa task-info';

        $routeName = 'admin.task-info.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\TaskInfoController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo task-info';

        $routeName = 'admin.task-info.tree3';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\TaskInfoController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree TaskInfo';
    });

});


Route2::prefix('member')->group(function () {

    Route2::prefix('task-info')->group(function () {
        $route_group_desc = 'Thao tác với TaskInfo';

        $routeName = 'member.task-info.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\TaskInfoController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách task-info';

        $routeName = 'member.task-info.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\TaskInfoController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa task-info';

        $routeName = 'member.task-info.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\TaskInfoController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo task-info';

        $routeName = 'admin.task-info.tree4';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\TaskInfoController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree TaskInfo';
    });

});
