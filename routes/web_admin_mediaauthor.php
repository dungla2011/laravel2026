<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('media-author')->group(function () {
        $route_group_desc = 'Thao tác với MediaAuthor';

        $routeName = 'admin.media-author.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\MediaAuthorController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách media-author';

        $routeName = 'admin.media-author.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\MediaAuthorController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa media-author';

        $routeName = 'admin.media-author.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\MediaAuthorController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo media-author';

        $routeName = 'admin.media-author.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\MediaAuthorController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree MediaAuthor';
    });

});
