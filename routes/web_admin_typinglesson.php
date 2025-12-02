<?php

use App\Components\Route2;

Route2::prefix('admin')->group(function () {

    Route2::prefix('typing-lesson')->group(function () {
        $route_group_desc = 'Thao tác với TypingLesson';

        $routeName = 'admin.typing-lesson.index';
        $r = Route2::get('/', [
            \App\Http\Controllers\TypingLessonController::class, 'index',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Xem danh sách typing-lesson';

        $routeName = 'admin.typing-lesson.edit';
        $r = Route2::get('/edit/{id}', [
            \App\Http\Controllers\TypingLessonController::class, 'edit',
        ])->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Sửa typing-lesson';

        $routeName = 'admin.typing-lesson.create';
        $r = Route2::get('/create',
            [\App\Http\Controllers\TypingLessonController::class, 'create'])
            ->name($routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tạo typing-lesson';

        $routeName = 'admin.typing-lesson.tree';
        $r = Route2::get('/tree', [
            \App\Http\Controllers\TypingLessonController::class, 'tree_index',
        ])->name($routeName); //->middleware("can:".$routeName);
        $r->middleware('can:'.$routeName);
        $r->route_group_desc_ = $route_group_desc;
        $r->route_desc_ = 'Tree TypingLesson';
    });

});
