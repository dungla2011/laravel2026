<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('hr-time-sheet')->group(function () {
        $route_group_desc = 'Thao tác với HrTimeSheet';

        $routeName = 'admin.hr-time-sheet.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\HrTimeSheetController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách hr-time-sheet';

        $routeName = 'admin.hr-time-sheet.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\HrTimeSheetController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa hr-time-sheet';

        $routeName = 'admin.hr-time-sheet.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\HrTimeSheetController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo hr-time-sheet';

        $routeName = 'admin.hr-time-sheet.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\HrTimeSheetController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree HrTimeSheet';
    });

});
