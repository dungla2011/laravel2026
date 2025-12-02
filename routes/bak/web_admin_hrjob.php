<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('hr-job')->group(function () {
        $route_group_desc = 'Thao tác với HrJob';

        $routeName = 'admin.hr-job.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\HrJobController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách hr-job';

        $routeName = 'admin.hr-job.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\HrJobController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa hr-job';

        $routeName = 'admin.hr-job.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\HrJobController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo hr-job';

        $routeName = 'admin.hr-job.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\HrJobController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree HrJob';
    });

});
