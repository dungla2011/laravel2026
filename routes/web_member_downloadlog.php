<?php

use App\Components\Route2;

Route2::prefix('member')->group(function () {

    Route2::prefix('download-log')->group(function () {
        $route_group_desc = 'Thao tác với DownloadLog';

        $routeName = 'member.download-log.index_user_download_your_file';
        $r = Route2::get('/your-file', [
            \App\Http\Controllers\DownloadLogController::class, 'index_user_download_your_file',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Lịch sử tải file';
        $r->title_force_ = 'Lịch sử người dùng khác tải file của bạn';

        /////////////////////////
        $routeName = 'member.download-log.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\DownloadLogController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách download-log';
        $r->title_force_ = 'Lịch sử Bạn tải các file';

        $routeName = 'member.download-log.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\DownloadLogController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa download-log';

        $routeName = 'member.download-log.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\DownloadLogController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo download-log';

        $routeName = 'member.download-log.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\DownloadLogController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree DownloadLog';
    });

});
