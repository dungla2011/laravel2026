<?php

use App\Components\Route2;


$r = Route2::get('/get-user-id', [
    \App\Http\ControllerApi\UserControllerApi::class, 'getUserId',
])->name('admin.get-uid');

Route2::prefix('/member-user')->group(function () {

    $route_group_desc = 'API - Thao tác User Member';

    $routeName = 'api.member-user.update';
    $r = Route2::post('/update-member', [
        \App\Http\ControllerApi\UserControllerApi::class, 'update_member',
    ])->name($routeName);
//    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Update';

    $routeName = 'api.member-user.get';
    $r = Route2::get('/get-member', [
        \App\Http\ControllerApi\UserControllerApi::class, 'get_member',
    ])->name($routeName);
//    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Get';



});


Route2::prefix('/user')->group(function () {

    $nameModule = 'user';

    $route_group_desc = 'API - Thao tác User';
    $routeName = 'api.user.list';
    $r = Route2::get('/list', [
        \App\Http\ControllerApi\UserControllerApi::class, 'list',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'List User';

    $routeName = 'api.user.add';
    $r = Route2::post('add', [
        \App\Http\ControllerApi\UserControllerApi::class, 'add',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Add User';

    $routeName = 'api.'.$nameModule.'.delete';
    $r = Route2::get('/delete', [
        \App\Http\ControllerApi\UserControllerApi::class, 'delete',
    ])->name($routeName)->where('name', '.*');
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Xóa User';

    $routeName = 'api.user.update';
    $r = Route2::post('/update/{id}', [
        \App\Http\ControllerApi\UserControllerApi::class, 'update',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Update';

    $routeName = 'api.user.get';
    $r = Route2::get('/get/{id}', [
        \App\Http\ControllerApi\UserControllerApi::class, 'get',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Get';

    $routeName = 'api.user.search';
    $r = Route2::match(['GET', 'POST'], 'search', [
        \App\Http\ControllerApi\UserControllerApi::class, 'search',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Search User';

    $routeName = 'api.user.update-multi';
    $r = Route2::post('/update-multi', [
        \App\Http\ControllerApi\UserControllerApi::class, 'update_multi',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Cập nhật User - multi';

    $routeName = 'api.user.get-api-token';
    $r = Route2::post('/get-api-token', [
        \App\Http\ControllerApi\UserControllerApi::class, 'getApiToken',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Lấy Token API của user';

    $routeName = 'api.user.undelete';
    $r = Route2::get('/un-delete', [
        \App\Http\ControllerApi\UserControllerApi::class, 'un_delete',
    ])->name($routeName)->where('name', '.*');
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Khôi phục user';

});
