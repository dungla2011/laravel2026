<?php

use App\Components\Route2;

Route2::prefix('product-bak')->group(function () {

    $route_group_desc = 'Thao tác sản phẩm';

    $routeName = 'admin.product-bak.index';
    $r = Route2::get('/', [
        \App\Http\Controllers\AdminProductController::class, 'index',
    ])->name($routeName)->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Liệt kê sản phẩm';

    $routeName = 'admin.product-bak.create';
    $r = Route2::get('/create',
        [
            \App\Http\Controllers\AdminProductController::class, 'create',
        ])->name($routeName)->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Tạo sản phẩm';

    $routeName = 'admin.product-bak.add';
    $r = Route2::post('/add', [
        \App\Http\Controllers\AdminProductController::class, 'store',
    ])->name($routeName)->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Thêm sản phẩm';

    $routeName = 'admin.product-bak.edit';
    $r = Route2::get('/edit/{id}', [
        \App\Http\Controllers\AdminProductController::class, 'edit',
    ])->name($routeName)->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Sửa sản phẩm';

    $routeName = 'admin.product-bak.update';
    $r = Route2::post('/update/{id}', [
        \App\Http\Controllers\AdminProductController::class, 'update',
    ])->name($routeName)->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Cập nhật sản phẩm';

    $routeName = 'admin.product-bak.delete';
    $r = Route2::get('/delete/{id}', [
        \App\Http\Controllers\AdminProductController::class, 'delete',
    ])->name($routeName)->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Xóa sản phẩm';

    $routeName = 'admin.product-bak.search';
    $r = Route2::get('/search', [
        \App\Http\Controllers\AdminProductController::class, 'search',
    ])->name($routeName)->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Tìm sản phẩm';
});
