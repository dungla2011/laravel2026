<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('event-user-payment')->group(function () {
        $route_group_desc = 'Thao tác với EventUserPayment';

        $routeName = 'admin.event-user-payment.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\EventUserPaymentController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách event-user-payment';

        $routeName = 'admin.event-user-payment.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\EventUserPaymentController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa event-user-payment';

        $routeName = 'admin.event-user-payment.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\EventUserPaymentController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo event-user-payment';

        $routeName = 'admin.event-user-payment.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\EventUserPaymentController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree EventUserPayment';
    });

});
