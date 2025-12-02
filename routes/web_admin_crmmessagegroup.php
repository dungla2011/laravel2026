<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('crm-message-group')->group(function () {
        $route_group_desc = 'Thao tác với CrmMessageGroup';

        $routeName = 'admin.crm-message-group.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\CrmMessageGroupController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách crm-message-group';

        $routeName = 'admin.crm-message-group.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\CrmMessageGroupController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa crm-message-group';

        $routeName = 'admin.crm-message-group.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\CrmMessageGroupController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo crm-message-group';

        $routeName = 'admin.crm-message-group.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\CrmMessageGroupController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree CrmMessageGroup';
    });

});
