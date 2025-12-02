<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('media-item')->group(function () {
        $route_group_desc = 'Thao tác với MediaItem';

        $routeName = 'admin.media-item.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\MediaItemController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách media-item';

        $routeName = 'admin.media-item.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\MediaItemController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa media-item';

        $routeName = 'admin.media-item.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\MediaItemController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo media-item';

        $routeName = 'admin.media-item.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\MediaItemController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree MediaItem';
    });

});
