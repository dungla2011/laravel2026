<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('event-payment')->group(function () {
        $route_group_desc = 'Thao tác với EventPayment';

        $routeName = 'admin.event-payment.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\EventPaymentController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách event-payment';

        $routeName = 'admin.event-payment.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\EventPaymentController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa event-payment';

        $routeName = 'admin.event-payment.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\EventPaymentController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo event-payment';

        $routeName = 'admin.event-payment.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\EventPaymentController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree EventPayment';
    });

});
