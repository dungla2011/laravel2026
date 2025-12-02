<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('media-cat')->group(function () {
        $route_group_desc = 'Thao tác với MediaCat';

        $routeName = 'admin.media-cat.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\MediaCatController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách media-cat';

        $routeName = 'admin.media-cat.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\MediaCatController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa media-cat';

        $routeName = 'admin.media-cat.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\MediaCatController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo media-cat';

        $routeName = 'admin.media-cat.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\MediaCatController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree MediaCat';
    });

});
