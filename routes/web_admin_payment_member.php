<?php

use App\Components\Route2;

Route2::prefix('member')->group(function () {

    Route2::prefix('payment')->group(function () {
        $route_group_desc = 'Thao tác với Payment';

        $routeName = 'member.payment.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\PaymentController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách payment';

        $routeName = 'member.payment.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\PaymentController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa payment';

        $routeName = 'member.payment.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\PaymentController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo payment';

        $routeName = 'member.payment.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\PaymentController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree Payment';
    });

});
