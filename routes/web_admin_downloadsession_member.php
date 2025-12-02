<?php

use App\Components\Route2;

Route2::prefix('member')->group(function () {

    Route2::prefix('download-session')->group(function () {
        $route_group_desc = 'Thao tác với TmpDownloadSession';


        //file duoc tai
        $routeName = 'member.download-session.index_user_download_your_file';
        $r = Route2::get('/your-file', [
            \App\Http\Controllers\TmpDownloadSessionController::class, 'index_user_download_your_file',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Lịch sử tải file';
        $r->title_force_ = 'Lịch sử người dùng khác tải file của bạn';

        ///

        $routeName = 'member.download-session.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\TmpDownloadSessionController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách download-session';

        $routeName = 'member.download-session.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\TmpDownloadSessionController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa download-session';

        $routeName = 'member.download-session.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\TmpDownloadSessionController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo download-session';

        $routeName = 'member.download-session.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\TmpDownloadSessionController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree TmpDownloadSession';
    });

});
