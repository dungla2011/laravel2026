<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('quiz-question')->group(function () {
        $route_group_desc = 'Thao tác với QuizQuestion';

        $routeName = 'admin.quiz-question.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\QuizQuestionController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách quiz-question';

        $routeName = 'admin.quiz-question.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\QuizQuestionController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa quiz-question';

        $routeName = 'admin.quiz-question.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\QuizQuestionController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo quiz-question';

        $routeName = 'admin.quiz-question.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\QuizQuestionController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree QuizQuestion';
    });

});
