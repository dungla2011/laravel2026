<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('quiz-session-info-test')->group(function () {
        $route_group_desc = 'Thao tác với QuizSessionInfoTest';

        $routeName = 'admin.quiz-session-info-test.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\QuizSessionInfoTestController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách quiz-session-info-test';

        $routeName = 'admin.quiz-session-info-test.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\QuizSessionInfoTestController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa quiz-session-info-test';

        $routeName = 'admin.quiz-session-info-test.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\QuizSessionInfoTestController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo quiz-session-info-test';

        $routeName = 'admin.quiz-session-info-test.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\QuizSessionInfoTestController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree QuizSessionInfoTest';
    });

});
