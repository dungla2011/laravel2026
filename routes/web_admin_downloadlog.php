<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('download-log')->group(function () {
        $route_group_desc = 'Thao tác với DownloadLog';

        $routeName = 'admin.download-log.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\DownloadLogController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách download-log';

        $routeName = 'admin.download-log.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\DownloadLogController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa download-log';

        $routeName = 'admin.download-log.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\DownloadLogController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo download-log';

        $routeName = 'admin.download-log.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\DownloadLogController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree DownloadLog';
    });

});
