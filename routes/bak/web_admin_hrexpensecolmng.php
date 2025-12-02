<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('hr-expense-col-mng')->group(function () {
        $route_group_desc = 'Thao tác với HrExpenseColMng';

        $routeName = 'admin.hr-expense-col-mng.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\HrExpenseColMngController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách hr-expense-col-mng';

        $routeName = 'admin.hr-expense-col-mng.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\HrExpenseColMngController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa hr-expense-col-mng';

        $routeName = 'admin.hr-expense-col-mng.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\HrExpenseColMngController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo hr-expense-col-mng';

        $routeName = 'admin.hr-expense-col-mng.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\HrExpenseColMngController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree HrExpenseColMng';
    });

});
