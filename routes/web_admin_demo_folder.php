<?php

use App\Components\Route2;
use Illuminate\Support\Facades\Route;

Route2::prefix('admin')->group(function () {

    Route2::prefix('demo-folder')->group(function () {
        $route_group_desc = 'Thao tác với folder';

        $routeName = 'admin.demo-folder.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\DemoFolderController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách  folder';

        $routeName = 'admin.demo-folder.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\DemoFolderController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa  folder';

        $routeName = 'admin.demo-folder.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\DemoFolderController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo  folder';
        //
        //        $routeName = "admin.demo-folder.add";
        //        $r = Route2::post("/add", [
        //            \App\Http\Controllers\DemoFolderController::class, 'store'
        //        ])->name($routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Thêm  folder';
        //
        //
        //        $routeName = "admin.demo-folder.update";
        //        $r = Route2::post("/update/{id}", [
        //            \App\Http\Controllers\DemoFolderController::class, 'update'
        //        ])->name($routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Cập nhật  folder';
        ////
        //        $routeName = "admin.demo-folder.delete";
        //        $r = Route2::get("/delete/{id}", [
        //            \App\Http\Controllers\DemoFolderController::class, 'delete'
        //        ])->name($routeName);//->middleware("can:".$routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Xóa  folder';

        $routeName = 'admin.demo-folder.index_tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\DemoFolderController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree  folder';

    });

});
