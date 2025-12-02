<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('partner-info')->group(function () {
        $route_group_desc = 'Thao tác với PartnerInfo';

        $routeName = 'admin.partner-info.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\PartnerInfoController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách partner-info';

        $routeName = 'admin.partner-info.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\PartnerInfoController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa partner-info';

        $routeName = 'admin.partner-info.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\PartnerInfoController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo partner-info';

        $routeName = 'admin.partner-info.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\PartnerInfoController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree PartnerInfo';
    });

});
