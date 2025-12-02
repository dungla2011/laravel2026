<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('test-mongo1')->group(function () {
        $route_group_desc = 'Thao tác với TestMongo1';

        $routeName = 'admin.test-mongo1.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\TestMongo1Controller::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách test-mongo1';

        $routeName = 'admin.test-mongo1.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\TestMongo1Controller::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa test-mongo1';

        $routeName = 'admin.test-mongo1.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\TestMongo1Controller::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo test-mongo1';

        $routeName = 'admin.test-mongo1.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\TestMongo1Controller::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree TestMongo1';
    });

});
