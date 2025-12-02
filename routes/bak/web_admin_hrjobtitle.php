<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('hr-job-title')->group(function () {
        $route_group_desc = 'Thao tác với HrJobTitle';

        $routeName = 'admin.hr-job-title.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\HrJobTitleController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách hr-job-title';

        $routeName = 'admin.hr-job-title.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\HrJobTitleController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa hr-job-title';

        $routeName = 'admin.hr-job-title.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\HrJobTitleController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo hr-job-title';

        $routeName = 'admin.hr-job-title.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\HrJobTitleController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree HrJobTitle';
    });

});
