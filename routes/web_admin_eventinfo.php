<?php

use App\Components\Route2;

$route_group_desc = 'Thao tác với EventInfo';
$routeName = 'public.event-info.confirm-eventGet';
$r = Route2::match(['get', 'post'], '/user-confirm-event', [
    \App\Http\Controllers\EventInfoController::class, 'userConfirmEvent',
])->name($routeName); //->middleware("can:".$routeName);
//        $r->middleware("can:" . $routeName);
$r->route_group_desc_ = $route_group_desc;
$r->route_desc_ = 'Confirm Event';


$routeName = 'public.event-info.confirm-eventGet1';
$r = Route2::match(['get', 'post'], '/user-confirm-event/id/{id}/data_ev/{data_ev}', [
    \App\Http\Controllers\EventInfoController::class, 'userConfirmEvent',
])->name($routeName); //->middleware("can:".$routeName);
//        $r->middleware("can:" . $routeName);
$r->route_group_desc_ = $route_group_desc;
$r->route_desc_ = 'Confirm Event';

$routeName = 'public.event-info.confirm-eventGet2';
$r = Route2::match(['get', 'post'], '/user-confirm-event/data/{data}', [
    \App\Http\Controllers\EventInfoController::class, 'userConfirmEvent',
])->name($routeName); //->middleware("can:".$routeName);
//        $r->middleware("can:" . $routeName);
$r->route_group_desc_ = $route_group_desc;
$r->route_desc_ = 'Confirm Event';




//$route_group_desc = 'Thao tác với EventInfo';
//$routeName = 'public.event-info.confirm-eventPost';
//$r = Route2::post('/user-confirm-event', [
//    \App\Http\Controllers\EventInfoController::class, 'userConfirmEvent',
//])->name($routeName); //->middleware("can:".$routeName);
////        $r->middleware("can:" . $routeName);
//$r->route_group_desc_ = $route_group_desc;
//$r->route_desc_ = 'Confirm Event';

Route2::prefix('admin')->group(function () {

    Route2::prefix('event-info')->group(function () {
        $route_group_desc = 'Thao tác với EventInfo';

        $routeName = 'admin.event-info.report';
        $r = Route2::get('/report', [
            \App\Http\Controllers\EventInfoController::class, 'report',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'event-report';

        $routeName = 'admin.event-info.report-sum';
        $r = Route2::get('/report-sum', [
            \App\Http\Controllers\EventInfoController::class, 'report_sum',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'event-report-sum';


        $routeName = 'admin.event-info.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\EventInfoController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách event-info';

        $routeName = 'admin.event-info.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\EventInfoController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa event-info';

        $routeName = 'admin.event-info.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\EventInfoController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo event-info';

        $routeName = 'admin.event-info.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\EventInfoController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree EventInfo';

        //

    });

});
