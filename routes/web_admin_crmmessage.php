<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('crm-message')->group(function () {
        $route_group_desc = 'Thao tác với CrmMessage';

        $routeName = 'admin.crm-message.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\CrmMessageController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách crm-message';

        $routeName = 'admin.crm-message.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\CrmMessageController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa crm-message';

        $routeName = 'admin.crm-message.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\CrmMessageController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo crm-message';

        $routeName = 'admin.crm-message.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\CrmMessageController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree CrmMessage';
    });

});
