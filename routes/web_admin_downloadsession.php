<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('download-session')->group(function () {
        $route_group_desc = 'Thao tác với TmpDownloadSession';

        $routeName = 'admin.download-session.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\TmpDownloadSessionController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách download-session';

        $routeName = 'admin.download-session.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\TmpDownloadSessionController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa download-session';

        $routeName = 'admin.download-session.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\TmpDownloadSessionController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo download-session';

        $routeName = 'admin.download-session.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\TmpDownloadSessionController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree TmpDownloadSession';
    });

});
