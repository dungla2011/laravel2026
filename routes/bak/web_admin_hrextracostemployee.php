<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('hr-extra-cost-employee')->group(function () {
        $route_group_desc = 'Thao tác với HrExtraCostEmployee';

        $routeName = 'admin.hr-extra-cost-employee.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\HrExtraCostEmployeeController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách hr-extra-cost-employee';

        $routeName = 'admin.hr-extra-cost-employee.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\HrExtraCostEmployeeController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa hr-extra-cost-employee';

        $routeName = 'admin.hr-extra-cost-employee.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\HrExtraCostEmployeeController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo hr-extra-cost-employee';

        $routeName = 'admin.hr-extra-cost-employee.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\HrExtraCostEmployeeController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree HrExtraCostEmployee';
    });

});
