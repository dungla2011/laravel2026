<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('pay-moneylog')->group(function () {
        $route_group_desc = 'Thao tác với PayMoneylog';

        $routeName = 'admin.pay-moneylog.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\PayMoneylogController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách pay-moneylog';

        $routeName = 'admin.pay-moneylog.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\PayMoneylogController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa pay-moneylog';

        $routeName = 'admin.pay-moneylog.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\PayMoneylogController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo pay-moneylog';

        $routeName = 'admin.pay-moneylog.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\PayMoneylogController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree PayMoneylog';
    });

});
