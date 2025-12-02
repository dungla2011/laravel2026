<?php

use App\Components\Route2;

Route2::prefix('member')->group(function () {


    $route_group_desc = 'Thao tác với Uploader';
    $routeName = 'uploader.index.4s';
    $r = Route2::get('/uploader', [
        \App\Http\Controllers\MemberController::class, 'uploader',
    ])->name($routeName);//->middleware('can:' . $routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Member uploader';



    ///////////////////

    $route_group_desc = 'Member zone';
    $routeName = 'member.index';
    $r = Route2::get('/', [
        \App\Http\Controllers\MemberController::class, 'index',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Member Index';

    $routeName = 'member.set-password';
    $r = Route2::get('/set-password', [
        \App\Http\Controllers\MemberController::class, 'setPassword',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Member setpw';

    $routeName = 'member.set-password.post';
    $r = Route2::post('/set-password', [
        \App\Http\Controllers\MemberController::class, 'setPassword',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Member setpw post';

});
//
//$routeName = "member.set-password";
//Route2::get('/member/set-password', [
//    \App\Http\Controllers\MemberController::class, 'setPassword'
//])->name("$routeName")->middleware("can:" . $routeName);;
//Route2::post('/member/set-password', [
//    \App\Http\Controllers\MemberController::class, 'setPassword'
//])->name("$routeName")->middleware("can:" . $routeName);;
