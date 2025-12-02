<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    $route_group_desc = 'Report lương các  nhóm';
    $routeName = 'admin.hr-salary-month-user.final';
    $r = Route2::get('/report-salary-final', [
        \App\Http\Controllers\HrSalaryMonthUserController::class, 'final_all_tree',
    ])->name($routeName); //->middleware("can:".$routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Tree HrSalaryMonthUser all tree';

    Route2::prefix('hr-salary-month-user')->group(function () {
        $route_group_desc = 'Thao tác với HrSalaryMonthUser';

        $routeName = 'admin.hr-salary-month-user.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\HrSalaryMonthUserController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách hr-salary-month-user';

        $routeName = 'admin.hr-salary-month-user.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\HrSalaryMonthUserController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa hr-salary-month-user';

        $routeName = 'admin.hr-salary-month-user.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\HrSalaryMonthUserController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo hr-salary-month-user';

        $routeName = 'admin.hr-salary-month-user.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\HrSalaryMonthUserController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree HrSalaryMonthUser';

        ///
        $routeName = 'admin.hr-salary-month-user.report';
        $r = Route2::get('/report', [
            \App\Http\Controllers\HrSalaryMonthUserController::class, 'report',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree HrSalaryMonthUser report';

        $routeName = 'admin.hr-salary-month-user.report2';
        $r = Route2::get('/report2', [
            \App\Http\Controllers\HrSalaryMonthUserController::class, 'report2',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree HrSalaryMonthUser report2';

        ///
        $routeName = 'admin.hr-salary-month-user.report-times';
        $r = Route2::get('/report-times', [
            \App\Http\Controllers\HrSalaryMonthUserController::class, 'reportTimes',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree HrSalaryMonthUser report time sheet';
    });

});
