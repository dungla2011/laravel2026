<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('quiz-test-question')->group(function () {
        $route_group_desc = 'Thao tác với QuizTestQuestion';

        $routeName = 'admin.quiz-test-question.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\QuizTestQuestionController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách quiz-test-question';

        $routeName = 'admin.quiz-test-question.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\QuizTestQuestionController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa quiz-test-question';

        $routeName = 'admin.quiz-test-question.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\QuizTestQuestionController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo quiz-test-question';

        $routeName = 'admin.quiz-test-question.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\QuizTestQuestionController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree QuizTestQuestion';
    });

});
