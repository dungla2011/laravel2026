<?php

use App\Components\Route2;

Route2::prefix('member')->group(function () {

    Route2::prefix('event-info')->group(function () {
        $route_group_desc = 'Thao tác với EventInfo';

        $routeName = 'member.event-info.report';
        $r = Route2::get('/report', [
            \App\Http\Controllers\EventInfoController::class, 'report',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'event-report';

        $routeName = 'member.event-info.report-sum';
        $r = Route2::get('/report-sum', [
            \App\Http\Controllers\EventInfoController::class, 'report_sum',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'event-report-sum';


        $routeName = 'member.event-info.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\EventInfoController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách event-info';

        $routeName = 'member.event-info.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\EventInfoController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa event-info';

        $routeName = 'member.event-info.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\EventInfoController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo event-info';

        $routeName = 'member.event-info.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\EventInfoController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree EventInfo';

        //

    });

});
