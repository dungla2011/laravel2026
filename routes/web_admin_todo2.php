<?php

use App\Components\Route2;
use Illuminate\Support\Facades\Route;

Route2::prefix('admin')->group(function () {

    Route2::prefix('todo2')->group(function () {
        $route_group_desc = 'Thao tác với Todo2';

        $routeName = 'admin.todo2.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\Todo2Controller::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách Todo2';

        $routeName = 'admin.todo2.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\Todo2Controller::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa Todo2';

        $routeName = 'admin.todo2.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\Todo2Controller::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo Todo2';

        //        $routeName = "admin.todo2.add";
        //        $r = Route2::post("/add", [
        //            \App\Http\Controllers\Todo2UploadController::class, 'store'
        //        ])->name($routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Thêm Todo2';
        ////
        ////
        //        $routeName = "admin.todo2.update";
        //        $r = Route2::post("/update/{id}", [
        //            \App\Http\Controllers\Todo2UploadController::class, 'update'
        //        ])->name($routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Cập nhật Todo2';
        ////
        //        $routeName = "admin.todo2.delete";
        //        $r = Route2::get("/delete/{id}", [
        //            \App\Http\Controllers\Todo2UploadController::class, 'delete'
        //        ])->name($routeName);//->middleware("can:".$routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Xóa Todo2';

        //
        //        $routeName = "admin.todo2-tree.index";
        //        $r = Route2::get("/tree", [
        //            \App\Http\Controllers\Todo2UploadController::class, 'tree_index'
        //        ])->name($routeName);//->middleware("can:".$routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Tree  folder';
        //
        //
        //        $routeName = "admin.todo2.upload";
        //        $r = Route2::get("/upload", [
        //            \App\Http\Controllers\Todo2UploadController::class, 'upload'
        //        ])->name($routeName);//->middleware("can:".$routeName);
        //        $r->middleware("can:" . $routeName);
        //        $r->route_group_desc_ = $route_group_desc;
        //        $r->route_desc_ = 'Todo2 upload';

    });

});
