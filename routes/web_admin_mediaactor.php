<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('media-actor')->group(function () {
        $route_group_desc = 'Thao tác với MediaActor';

        $routeName = 'admin.media-actor.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\MediaActorController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách media-actor';

        $routeName = 'admin.media-actor.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\MediaActorController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa media-actor';

        $routeName = 'admin.media-actor.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\MediaActorController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo media-actor';

        $routeName = 'admin.media-actor.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\MediaActorController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree MediaActor';
    });

});
