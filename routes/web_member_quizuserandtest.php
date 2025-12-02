<?php

use App\Components\Route2;

Route2::prefix('member')->group(function () {

    Route2::prefix('quiz-user-and-test')->group(function () {
        $route_group_desc = 'Thao tác với QuizUserAndTest';

        $routeName = 'member.quiz-user-and-test.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\QuizUserAndTestController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách quiz-user-and-test';

        $routeName = 'member.quiz-user-and-test.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\QuizUserAndTestController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa quiz-user-and-test';

        $routeName = 'member.quiz-user-and-test.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\QuizUserAndTestController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo quiz-user-and-test';

        $routeName = 'member.quiz-user-and-test.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\QuizUserAndTestController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree QuizUserAndTest';

        $routeName = 'member.quiz-user-and-test.doTest';
        $r = Route2::get('/doTest', [
            \App\Http\Controllers\QuizUserAndTestController::class, 'doTest',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree QuizUserAndTest';
    });

});
