<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('media-link')->group(function () {
        $route_group_desc = 'Thao tác với MediaLink';

        $routeName = 'admin.media-link.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\MediaLinkController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách media-link';

        $routeName = 'admin.media-link.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\MediaLinkController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa media-link';

        $routeName = 'admin.media-link.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\MediaLinkController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo media-link';

        $routeName = 'admin.media-link.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\MediaLinkController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree MediaLink';
    });

});
