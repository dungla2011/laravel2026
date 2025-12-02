<?php

use App\Components\Route2;

Route2::prefix('member')->group(function () {

    Route2::prefix('file')->group(function () {
        $route_group_desc = 'Thao tác với Demo';

        $routeName = 'member.file.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\FileUploadController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách demo';

        $routeName = 'member.file.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\FileUploadController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa demo';

        $routeName = 'member.file.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\FileUploadController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo demo';

        $routeName = 'member.file-tree.index';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\FileUploadController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree  folder';

        $routeName = 'member.file.upload';
        $r = Route2::get('/upload', [
            \App\Http\Controllers\FileUploadController::class, 'upload',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'File upload';

    });

});
