<?php

use App\Components\Route2;
use Illuminate\Support\Facades\Route;

Route2::prefix('admin')->group(function () {

    Route2::prefix('menu-tree')->group(function () {
        $route_group_desc = 'Thao tác với folder';

        $routeName = 'admin.menu-tree.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\MenuTreeController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách  folder';

        $routeName = 'admin.menu-tree.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\MenuTreeController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa  folder';

        $routeName = 'admin.menu-tree.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\MenuTreeController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo  folder';
        //
        //        $routeName = "admin.menu-tree.add";
        //        $r = Route2::post("/add", [
        //            \App\Http\Controllers\MenuTreeController::class, 'store'
        //        ])->name($routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Thêm  folder';
        ////
        ////
        //        $routeName = "admin.menu-tree.update";
        //        $r = Route2::post("/update/{id}", [
        //            \App\Http\Controllers\MenuTreeController::class, 'update'
        //        ])->name($routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Cập nhật  folder';
        ////
        //        $routeName = "admin.menu-tree.delete";
        //        $r = Route2::get("/delete/{id}", [
        //            \App\Http\Controllers\MenuTreeController::class, 'delete'
        //        ])->name($routeName);//->middleware("can:".$routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Xóa  folder';

        $routeName = 'admin.menu-tree.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\MenuTreeController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree  folder';

    });

});
