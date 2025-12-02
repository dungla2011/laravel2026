<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('uploader-info')->group(function () {
        $route_group_desc = 'Thao tác với UploaderInfo';

        $routeName = 'admin.uploader-info.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\UploaderInfoController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách uploader-info';

        $routeName = 'admin.uploader-info.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\UploaderInfoController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa uploader-info';

        $routeName = 'admin.uploader-info.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\UploaderInfoController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo uploader-info';

        $routeName = 'admin.uploader-info.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\UploaderInfoController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree UploaderInfo';
    });

});
