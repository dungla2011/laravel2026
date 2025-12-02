<?php

use App\Components\Route2;

$route_group_desc = 'Thao tác với NetworkMaketting';
$routeName = 'network-marketing.abc.2023';
$r = Route2::get('/network-marketing/abc/{idf}', [
    \App\Http\Controllers\MarketingController::class, 'lien_ket_mar_abc_cookie',
])->name($routeName);
$r->route_group_desc_ = $route_group_desc;
$r->route_desc_ = 'Xem NetworkMaketting';

Route2::prefix('/member')->group(function () {

    $route_group_desc = 'Thao tác với NetworkMaketting';
    $routeName = 'member.network-marketing.abc.get.id';
    $r = Route2::get('/network-marketing/abc/{idf}', [
        \App\Http\Controllers\MarketingController::class, 'lien_ket_mar_abc',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Xem NetworkMaketting';

    $routeName = 'member.network-marketing.abc.post.id';
    $r = Route2::post('/network-marketing/abc/{idf}', [
        \App\Http\Controllers\MarketingController::class, 'lien_ket_mar_abc',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Xem NetworkMaketting';

    $routeName = 'member.network-marketing.abc.get';
    $r = Route2::get('/network-marketing/abc', [
        \App\Http\Controllers\MarketingController::class, 'lien_ket_mar_abc',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Xem NetworkMaketting';

    $routeName = 'member.network-marketing.abc.post';
    $r = Route2::post('/network-marketing/abc', [
        \App\Http\Controllers\MarketingController::class, 'lien_ket_mar_abc',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Xem NetworkMaketting';

});
