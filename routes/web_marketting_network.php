<?php

use App\Components\Route2;

$route_group_desc = 'Thao tác với NetworkMaketting';
$routeName = 'network-marketing.shb.2023';
$r = Route2::get('/network-marketing/shb/{idf}', [
    \App\Http\Controllers\MarketingController::class, 'lien_ket_mar_shb_cookie',
])->name($routeName);
$r->route_group_desc_ = $route_group_desc;
$r->route_desc_ = 'Xem NetworkMaketting';

Route2::prefix('/member')->group(function () {

    $route_group_desc = 'Thao tác với NetworkMaketting';
    $routeName = 'member.network-marketing.get.id';
    $r = Route2::get('/network-marketing/shb/{idf}', [
        \App\Http\Controllers\MarketingController::class, 'lien_ket_mar_shb',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Xem NetworkMaketting';

    $routeName = 'member.network-marketing.post.id';
    $r = Route2::post('/network-marketing/shb/{idf}', [
        \App\Http\Controllers\MarketingController::class, 'lien_ket_mar_shb',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Xem NetworkMaketting';

    $routeName = 'member.network-marketing.get';
    $r = Route2::get('/network-marketing/shb', [
        \App\Http\Controllers\MarketingController::class, 'lien_ket_mar_shb',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Xem NetworkMaketting';

    $routeName = 'member.network-marketing.post';
    $r = Route2::post('/network-marketing/shb', [
        \App\Http\Controllers\MarketingController::class, 'lien_ket_mar_shb',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Xem NetworkMaketting';

});
