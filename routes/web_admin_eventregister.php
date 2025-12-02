<?php

use App\Components\Route2;

$routeName = 'public.event-register.index1';
Route2::group(['prefix' => '{locale?}'], function ()
{
    $routeName = 'public.event-register.index2';
    $r = Route2::match(['get', 'post'], '/event-register/{id}', [
        \App\Http\Controllers\EventRegisterController::class, 'register',
    ])->name($routeName);
    $r->middleware('setlocale');
    $r->route_group_desc_ = "EventRegPublic";
    $r->route_desc_ = 'Dang ky event-register';
}
);

$routeName = 'public.event-register.index3';
$r = Route2::match(['get', 'post'], '/event-register/{id}', [
    \App\Http\Controllers\EventRegisterController::class, 'register',
])->name($routeName);
//->middleware('setlocale');
$r->route_group_desc_ = "EventRegPublic";
$r->route_desc_ = 'Dang ky event-register';

$routeName = 'public.event-register.verifyEmail';
$r = Route2::match(['get'], '/event-register/verify-email/{reg_code}', [
    \App\Http\Controllers\EventRegisterController::class, 'verifyEmail',
])->name($routeName);
$r->route_group_desc_ = "verifyEmail Confirm";
$r->route_desc_ = 'verifyEmail';

Route2::prefix('admin')->group(function () {

    Route2::prefix('event-register')->group(function () {
        $route_group_desc = 'Thao tác với EventRegister';

        $routeName = 'admin.event-register.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\EventRegisterController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách event-register';

        $routeName = 'admin.event-register.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\EventRegisterController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa event-register';

        $routeName = 'admin.event-register.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\EventRegisterController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo event-register';

        $routeName = 'admin.event-register.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\EventRegisterController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree EventRegister';
    });

});
