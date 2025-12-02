<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('face-data')->group(function () {
        $route_group_desc = 'Thao tác với FaceData';

        $routeName = 'admin.face-data.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\FaceDataController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách face-data';

        $routeName = 'admin.face-data.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\FaceDataController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa face-data';

        $routeName = 'admin.face-data.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\FaceDataController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo face-data';

        $routeName = 'admin.face-data.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\FaceDataController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree FaceData';
    });

});
