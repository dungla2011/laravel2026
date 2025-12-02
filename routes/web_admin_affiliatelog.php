<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('affiliate-log')->group(function () {
        $route_group_desc = 'Thao tác với AffiliateLog';

        $routeName = 'admin.affiliate-log.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\AffiliateLogController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách affiliate-log';

        $routeName = 'admin.affiliate-log.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\AffiliateLogController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa affiliate-log';

        $routeName = 'admin.affiliate-log.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\AffiliateLogController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo affiliate-log';

        $routeName = 'admin.affiliate-log.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\AffiliateLogController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree AffiliateLog';
    });

});
