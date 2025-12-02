<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('hr-user-expense')->group(function () {
        $route_group_desc = 'Thao tác với HrUserExpense';

        $routeName = 'admin.hr-user-expense.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\HrUserExpenseController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách hr-user-expense';

        $routeName = 'admin.hr-user-expense.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\HrUserExpenseController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa hr-user-expense';

        $routeName = 'admin.hr-user-expense.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\HrUserExpenseController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo hr-user-expense';

        $routeName = 'admin.hr-user-expense.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\HrUserExpenseController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree HrUserExpense';
    });

});
