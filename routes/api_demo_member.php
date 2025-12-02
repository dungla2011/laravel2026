<?php

use App\Components\Route2;
use Illuminate\Support\Facades\Route;

Route2::prefix('/member-demo')->group(function () {

    $route_group_desc = 'API - Thao tác với Demo';

    $cls = \App\Http\ControllerApi\DemoControllerApi::class;
    $modelUsing_ = \App\Models\DemoTbl::class;

    $routeName = 'api.member-demo.list';
    $r = Route2::get('/list', [
        $cls, 'list',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Xem danh sách demo';
    $r->modelUsing_ = $modelUsing_;

    //    $routeName = "api.member-demo.create";
    //    $r = Route2::post("/create",
    //        [$cls, 'create'])
    //        ->name($routeName);
    //    $r->middleware("can:".$routeName);
    //    $r->route_group_desc_ = $route_group_desc;
    //    $r->route_desc_ = "Tạo demo";

    $routeName = 'api.member-demo.add';
    $r = Route2::post('/add', [
        $cls, 'add',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Thêm demo';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.member-demo.get';
    $r = Route2::get('/get/{id}', [
        $cls, 'get',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Lấy thông tin demo';
    $r->modelUsing_ = $modelUsing_;

    //    $routeName = "api.member-demo.edit";
    //    $r = Route2::get("/edit/{id}", [
    //        $cls, 'edit'
    //    ])->name($routeName);
    //    $r->middleware("can:".$routeName);
    //    $r->route_group_desc_ = $route_group_desc;
    //    $r->route_desc_ = "Sửa demo";
    //    $r->modelUsing_ = $modelUsing_;

    //
    $routeName = 'api.member-demo.update';
    $r = Route2::post('/update/{id}', [
        $cls, 'update',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Cập nhật demo';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.member-demo.update-multi';
    $r = Route2::post('/update-multi', [
        $cls, 'update_multi',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Cập nhật demo - multi';
    $r->modelUsing_ = $modelUsing_;
    $r->showApi_ = 0;

    //
    $routeName = 'api.member-demo.delete';
    $r = Route2::get('/delete', [
        $cls, 'delete',
    ])->name($routeName)->where('name', '.*');
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Xóa demo';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.member-demo.undelete';
    $r = Route2::get('/un-delete', [
        $cls, 'un_delete',
    ])->name($routeName)->where('name', '.*');
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Khôi phục demo';
    $r->modelUsing_ = $modelUsing_;

    //    $routeName = "api.member-demo.move";
    //    $r = Route2::get("/move", [
    //        $cls, 'tree_move',
    //    ])->name($routeName);
    //    $r->middleware("can:" . $routeName);
    //    $r->route_group_desc_ = $route_group_desc;
    //    $r->route_desc_ = "Move on tree";
    //    $r->modelUsing_ = $modelUsing_;

});
