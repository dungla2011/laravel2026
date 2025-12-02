<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('media-vendor')->group(function () {
        $route_group_desc = 'Thao tác với MediaVendor';

        $routeName = 'admin.media-vendor.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\MediaVendorController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách media-vendor';

        $routeName = 'admin.media-vendor.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\MediaVendorController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa media-vendor';

        $routeName = 'admin.media-vendor.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\MediaVendorController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo media-vendor';

        $routeName = 'admin.media-vendor.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\MediaVendorController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree MediaVendor';
    });

});
