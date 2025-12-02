<?php

use App\Components\Route2;

Route2::prefix('member')->group(function () {


    $routeName = 'member.event-summary.index';
    $r = Route2::match(['get'], '/eventSummary', [
        \App\Http\Controllers\EventInfoController::class, 'memberEventSummary',
    ])->name($routeName); //->middleware("can:".$routeName);
    $r->middleware("can:" . $routeName);
    $r->route_group_desc_ = "Event Sum";
    $r->route_desc_ = 'Confirm Event';


    Route2::prefix('event-register')->group(function () {
        $route_group_desc = 'Thao tác với EventRegister';

        $routeName = 'member.event-register.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\EventRegisterController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách event-register';

        $routeName = 'member.event-register.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\EventRegisterController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa event-register';

        $routeName = 'member.event-register.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\EventRegisterController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo event-register';

        $routeName = 'member.event-register.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\EventRegisterController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree EventRegister';
    });

});
