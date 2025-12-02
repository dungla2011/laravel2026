<?php

use App\Components\Route2;
use Illuminate\Support\Facades\Route;

Route2::prefix('/news')->group(function () {

    $route_group_desc = 'API - Thao tác với News';
    $nameModule = 'news';

    $cls = \App\Http\ControllerApi\NewsControllerApi::class;

    $routeName = 'api.'.$nameModule.'.list';
    $r = Route2::get('/list', [
        $cls, 'list',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Xem danh sách News';

    //    $routeName = "api.".$nameModule.".create";
    //    $r = Route2::post("/create",
    //        [$cls, 'create'])
    //        ->name($routeName);
    //    $r->middleware("can:".$routeName);
    //    $r->route_group_desc_ = $route_group_desc;
    //    $r->route_desc_ = "Tạo News";

    $routeName = 'api.'.$nameModule.'.add';
    $r = Route2::post('/add', [
        $cls, 'add',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Thêm News';

    $routeName = 'api.'.$nameModule.'.get';
    $r = Route2::get('/get/{id}', [
        $cls, 'get',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Lấy thông tin News';

    //    $routeName = "api.".$nameModule.".edit";
    //    $r = Route2::get("/edit/{id}", [
    //        $cls, 'edit'
    //    ])->name($routeName);
    //    $r->middleware("can:".$routeName);
    //    $r->route_group_desc_ = $route_group_desc;
    //    $r->route_desc_ = "Sửa News";
    //
    $routeName = 'api.'.$nameModule.'.update';
    $r = Route2::post('/update/{id}', [
        $cls, 'update',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Cập nhật News';

    $routeName = 'api.'.$nameModule.'.update-multi';
    $r = Route2::post('/update-multi', [
        $cls, 'update_multi',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Cập nhật News - multi';
    //
    $routeName = 'api.'.$nameModule.'.delete';
    $r = Route2::get('/delete', [
        $cls, 'delete',
    ])->name($routeName)->where('name', '.*');
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Xóa News';

    $routeName = 'api.'.$nameModule.'.undelete';
    $r = Route2::get('/un-delete', [
        $cls, 'un_delete',
    ])->name($routeName)->where('name', '.*');
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Khôi phục News';

    //    $routeName = "api." . $nameModule . ".move";
    //    $r = Route2::get("/move", [
    //        $cls, 'tree_move',
    //    ])->name($routeName);
    //    $r->middleware("can:" . $routeName);
    //    $r->route_group_desc_ = $route_group_desc;
    //    $r->route_desc_ = "Move on tree";

});
