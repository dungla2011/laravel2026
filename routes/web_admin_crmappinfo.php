<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('crm-app-info')->group(function () {
        $route_group_desc = 'Thao tác với CrmAppInfo';

        $routeName = 'admin.crm-app-info.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\CrmAppInfoController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách crm-app-info';

        $routeName = 'admin.crm-app-info.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\CrmAppInfoController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa crm-app-info';

        $routeName = 'admin.crm-app-info.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\CrmAppInfoController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo crm-app-info';

        $routeName = 'admin.crm-app-info.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\CrmAppInfoController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree CrmAppInfo';
    });

});
