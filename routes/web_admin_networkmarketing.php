<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('network-marketing')->group(function () {
        $route_group_desc = 'Thao tác với NetworkMarketing';

        $routeName = 'admin.network-marketing.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\NetworkMarketingController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách network-marketing';

        $routeName = 'admin.network-marketing.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\NetworkMarketingController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa network-marketing';

        $routeName = 'admin.network-marketing.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\NetworkMarketingController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo network-marketing';

        $routeName = 'admin.network-marketing.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\NetworkMarketingController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree NetworkMarketing';
    });

});
