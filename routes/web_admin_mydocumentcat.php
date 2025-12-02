<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('my-document-cat')->group(function () {
        $route_group_desc = 'Thao tác với MyDocumentCat';

        $routeName = 'admin.my-document-cat.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\MyDocumentCatController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách my-document-cat';

        $routeName = 'admin.my-document-cat.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\MyDocumentCatController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa my-document-cat';

        $routeName = 'admin.my-document-cat.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\MyDocumentCatController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo my-document-cat';

        $routeName = 'admin.my-document-cat.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\MyDocumentCatController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree MyDocumentCat';
    });

});
