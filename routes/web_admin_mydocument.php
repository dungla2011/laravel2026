<?php

use App\Components\Route2;

Route2::prefix('/tai-lieu')->group(function () {
    $route_group_desc = 'Xem Document';
    $routeName = 'public.my-document.item';
    $r = Route2::get('/chi-tiet', [
        \App\Http\Controllers\MyDocumentController::class, 'item',
    ])->name($routeName);
    //  $r->middleware("can:" . $routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Xem danh sách my-document';

    $route_group_desc = 'Xem Document';
    $routeName = 'public.my-document.folder';
    $r = Route2::get('/danh-muc', [
        \App\Http\Controllers\MyDocumentController::class, 'folder',
    ])->name($routeName);
    //  $r->middleware("can:" . $routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Xem danh sách my-document';

});

Route2::prefix('admin')->group(function () {

    Route2::prefix('my-document')->group(function () {
        $route_group_desc = 'Thao tác với MyDocument';

        $routeName = 'admin.my-document.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\MyDocumentController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách my-document';

        $routeName = 'admin.my-document.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\MyDocumentController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa my-document';

        $routeName = 'admin.my-document.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\MyDocumentController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo my-document';

        $routeName = 'admin.my-document.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\MyDocumentController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree MyDocument';
    });

});
