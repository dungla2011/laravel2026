<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('hr-contract')->group(function () {
        $route_group_desc = 'Thao tác với HrContract';

        $routeName = 'admin.hr-contract.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\HrContractController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách hr-contract';

        $routeName = 'admin.hr-contract.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\HrContractController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa hr-contract';

        $routeName = 'admin.hr-contract.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\HrContractController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo hr-contract';

        $routeName = 'admin.hr-contract.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\HrContractController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree HrContract';
    });

});
