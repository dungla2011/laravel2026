<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('file-refer')->group(function () {
        $route_group_desc = 'Thao tác với FileRefer';

        $routeName = 'admin.file-refer.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\FileReferController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách file-refer';

        $routeName = 'admin.file-refer.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\FileReferController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa file-refer';

        $routeName = 'admin.file-refer.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\FileReferController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo file-refer';

        $routeName = 'admin.file-refer.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\FileReferController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree FileRefer';
    });

});


Route2::prefix('member')->group(function () {

    Route2::prefix('file-refer')->group(function () {
        $route_group_desc = 'Thao tác với FileRefer';

        $routeName = 'member.file-refer.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\FileReferController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách file-refer';

        $routeName = 'member.file-refer.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\FileReferController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa file-refer';

        $routeName = 'member.file-refer.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\FileReferController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo file-refer';

        $routeName = 'member.file-refer.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\FileReferController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree FileRefer';
    });

});
