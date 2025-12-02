<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('hateco-certificate')->group(function () {
        $route_group_desc = 'Thao tác với HatecoCertificate';

        $routeName = 'admin.hateco-certificate.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\HatecoCertificateController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách hateco-certificate';

        $routeName = 'admin.hateco-certificate.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\HatecoCertificateController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa hateco-certificate';

        $routeName = 'admin.hateco-certificate.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\HatecoCertificateController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo hateco-certificate';

        $routeName = 'admin.hateco-certificate.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\HatecoCertificateController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree HatecoCertificate';
    });

});
