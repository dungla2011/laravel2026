<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('hr-config-session-org-id-salary')->group(function () {
        $route_group_desc = 'Thao tác với HrConfigSessionOrgIdSalary';

        $routeName = 'admin.hr-config-session-org-id-salary.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\HrConfigSessionOrgIdSalaryController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách hr-config-session-org-id-salary';

        $routeName = 'admin.hr-config-session-org-id-salary.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\HrConfigSessionOrgIdSalaryController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa hr-config-session-org-id-salary';

        $routeName = 'admin.hr-config-session-org-id-salary.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\HrConfigSessionOrgIdSalaryController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo hr-config-session-org-id-salary';

        $routeName = 'admin.hr-config-session-org-id-salary.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\HrConfigSessionOrgIdSalaryController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree HrConfigSessionOrgIdSalary';
    });

});
