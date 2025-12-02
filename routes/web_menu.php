<?php

use App\Components\Route2;
use Illuminate\Support\Facades\Route;

Route2::prefix('admin')->group(function () {
    Route2::prefix('menu')->group(function () {

        $routeName = 'admin.menu.create';
        $r = Route2::get('/create',
            [
                \App\Http\Controllers\MenuController::class, 'create',
            ])->name($routeName)->middleware('can:'.$routeName);
        $r->route_desc_ = 'Tạo Menu';

        $routeName = 'admin.menu.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\MenuController::class, 'index',
        ])->name($routeName)->middleware('can:'.$routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Danh sách Menu';

        $routeName = 'admin.menu.add';
        $r = Route2::post('/add', [
            \App\Http\Controllers\MenuController::class, 'store',
        ])->name($routeName)->middleware('can:'.$routeName);
        $r->route_desc_ = 'Ghi lại Menu';

        $routeName = 'admin.menu.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\MenuController::class, 'edit',
        ])->name($routeName)->middleware('can:'.$routeName);
        $r->route_desc_ = 'Edit Menu';

        $routeName = 'admin.menu.update';
        $r = Route2::post('/update/{id}', [
            \App\Http\Controllers\MenuController::class, 'update',
        ])->name($routeName)->middleware('can:'.$routeName);
        $r->route_desc_ = 'Cập nhật Menu';

        $routeName = 'admin.menu.delete';
        $r = Route2::get('/delete/{id}', [
            \App\Http\Controllers\MenuController::class, 'delete',
        ])->name($routeName)->middleware('can:'.$routeName);
        $r->route_desc_ = 'Xóa Menu';

    });
});
