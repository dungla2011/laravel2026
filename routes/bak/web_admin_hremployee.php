<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('hr-employee')->group(function () {
        $route_group_desc = 'Thao tác với HrEmployee';

        $routeName = 'admin.hr-employee.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\HrEmployeeController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách hr-employee';

        $routeName = 'admin.hr-employee.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\HrEmployeeController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa hr-employee';

        $routeName = 'admin.hr-employee.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\HrEmployeeController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo hr-employee';

        $routeName = 'admin.hr-employee.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\HrEmployeeController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree HrEmployee';
    });

});
