<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('news-folder')->group(function () {
        $route_group_desc = 'Thao tác với NewsFolder';

        $routeName = 'admin.news-folder.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\NewsFolderController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách news-folder';

        $routeName = 'admin.news-folder.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\NewsFolderController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa news-folder';

        $routeName = 'admin.news-folder.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\NewsFolderController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo news-folder';

        $routeName = 'admin.news-folder.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\NewsFolderController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree NewsFolder';
    });

});
