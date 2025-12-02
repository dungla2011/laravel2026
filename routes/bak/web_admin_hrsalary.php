<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('hr-salary')->group(function () {
        $route_group_desc = 'Thao tác với HrSalary';

        $routeName = 'admin.hr-salary.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\HrSalaryController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách hr-salary';

        $routeName = 'admin.hr-salary.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\HrSalaryController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa hr-salary';

        $routeName = 'admin.hr-salary.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\HrSalaryController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo hr-salary';

        $routeName = 'admin.hr-salary.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\HrSalaryController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree HrSalary';
    });

});
