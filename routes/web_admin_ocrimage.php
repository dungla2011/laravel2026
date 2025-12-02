<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('ocr-image')->group(function () {
        $route_group_desc = 'Thao tác với OcrImage';

        $routeName = 'admin.ocr-image.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\OcrImageController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách ocr-image';

        $routeName = 'admin.ocr-image.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\OcrImageController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa ocr-image';

        $routeName = 'admin.ocr-image.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\OcrImageController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo ocr-image';

        $routeName = 'admin.ocr-image.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\OcrImageController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree OcrImage';
    });

});
