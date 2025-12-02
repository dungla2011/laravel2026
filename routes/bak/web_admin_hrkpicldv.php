<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('hr-kpi-cldv')->group(function () {
        $route_group_desc = 'Thao tác với HrKpiCldv';

        $routeName = 'admin.hr-kpi-cldv.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\HrKpiCldvController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách hr-kpi-cldv';

        $routeName = 'admin.hr-kpi-cldv.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\HrKpiCldvController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa hr-kpi-cldv';

        $routeName = 'admin.hr-kpi-cldv.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\HrKpiCldvController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo hr-kpi-cldv';

        $routeName = 'admin.hr-kpi-cldv.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\HrKpiCldvController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree HrKpiCldv';
    });

});
