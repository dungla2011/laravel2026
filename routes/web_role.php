<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('role')->group(function () {
        $route_group_desc = 'Thao tác với Role';

        $routeName = 'admin.role.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\AdminRoleController::class, 'index',
        ])->name($routeName)->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách Role';

        $routeName = 'admin.role.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\AdminRoleController::class, 'create'])
            ->name($routeName)->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo Role';

        $routeName = 'admin.role.add';
        $r = Route2::post('/add', [
            \App\Http\Controllers\AdminRoleController::class, 'store',
        ])->name($routeName)->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Thêm Role';
        //
        $routeName = 'admin.role.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\AdminRoleController::class, 'edit',
        ])->name($routeName)->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa Role';
        //
        $routeName = 'admin.role.update';
        $r = Route2::post('/update/{id}', [
            \App\Http\Controllers\AdminRoleController::class, 'update',
        ])->name($routeName)->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Cập nhật Role';
        //
        $routeName = 'admin.role.delete';
        $r = Route2::get('/delete/{id}', [
            \App\Http\Controllers\AdminRoleController::class, 'delete',
        ])->name($routeName)->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xóa Role';

    });

});
