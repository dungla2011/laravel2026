<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('quiz-test')->group(function () {
        $route_group_desc = 'Thao tác với QuizTest';

        $routeName = 'admin.quiz-test.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\QuizTestController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách quiz-test';

        $routeName = 'admin.quiz-test.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\QuizTestController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa quiz-test';

        $routeName = 'admin.quiz-test.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\QuizTestController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo quiz-test';

        $routeName = 'admin.quiz-test.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\QuizTestController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree QuizTest';
    });

});
