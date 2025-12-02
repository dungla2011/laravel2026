<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('media-folder')->group(function () {
        $route_group_desc = 'Thao tác với MediaFolder';

        $routeName = 'admin.media-folder.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\MediaFolderController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách media-folder';

        $routeName = 'admin.media-folder.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\MediaFolderController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa media-folder';

        $routeName = 'admin.media-folder.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\MediaFolderController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo media-folder';

        $routeName = 'admin.media-folder.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\MediaFolderController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree MediaFolder';
    });

});
