<?php

use App\Components\Route2;

Route2::prefix('/quiz-test')->group(function () {

    $route_group_desc = 'API - Thao tác với QuizTest';
    $nameModule = 'quiz-test';
    $modelUsing_ = \App\Models\QuizTest::class;

    $cls = \App\Http\ControllerApi\QuizTestControllerApi::class;

    ///
    ///
    ///
    $routeName = 'api.'.$nameModule.'.postChoiceOfQues';
    $r = Route2::post('/postChoiceOfQues', [
        \App\Http\ControllerApi\QuizTestControllerApi::class, 'postChoiceOfQues',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Xem danh sách quiz-test';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.postQuestToTest';
    $r = Route2::post('/postQuestToTest', [
        \App\Http\ControllerApi\QuizTestControllerApi::class, 'postQuestToTest',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Xem danh sách quiz-test';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.addUserToClass';
    $r = Route2::post('/addUserToClass', [
        \App\Http\ControllerApi\QuizTestControllerApi::class, 'addUserToClass',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'add addUserToClass';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.addUserToTest';
    $r = Route2::post('/addUserToTest', [
        \App\Http\ControllerApi\QuizTestControllerApi::class, 'addUserToTest',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'add quiztest and user';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.resetBaiKiemTra';
    $r = Route2::post('/resetBaiKiemTra', [
        \App\Http\ControllerApi\QuizTestControllerApi::class, 'resetBaiKiemTra',
    ])->name($routeName);
    if ($r instanceof Route2);
    //    $r->middleware("can:" . $routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'add quiztest and user';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.doTestPostResult';
    $r = Route2::post('/doTestPostResult', [
        \App\Http\ControllerApi\QuizTestControllerApi::class, 'doTestPostResult',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'add doTestPostResult';
    $r->modelUsing_ = $modelUsing_;

    ////

    $routeName = 'api.'.$nameModule.'.list';
    $r = Route2::get('/list', [
        $cls, 'list',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Xem danh sách quiz-test';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.get';
    $r = Route2::get('/get/{id}', [
        $cls, 'get',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Lấy thông tin quiz-test';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.add';
    $r = Route2::post('/add', [
        $cls, 'add',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Thêm quiz-test';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.update';
    $r = Route2::post('/update/{id}', [
        $cls, 'update',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Cập nhật quiz-test';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.update-multi';
    $r = Route2::post('/update-multi', [
        $cls, 'update_multi',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Cập nhật quiz-test - multi';
    $r->modelUsing_ = $modelUsing_;
    $r->showApi_ = 0;
    //
    $routeName = 'api.'.$nameModule.'.delete';
    $r = Route2::get('/delete', [
        $cls, 'delete',
    ])->name($routeName)->where('name', '.*');
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Xóa quiz-test';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.undelete';
    $r = Route2::get('/un-delete', [
        $cls, 'un_delete',
    ])->name($routeName)->where('name', '.*');
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Khôi phục quiz-test';
    $r->modelUsing_ = $modelUsing_;

    ////FOR TREE - NẾU CÓ //////////////////////////////////////////////
    $routeName = 'api.'.$nameModule.'.tree-index';
    $r = Route2::get('/tree', [
        $cls, 'tree_index',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Tree list QuizTest';

    $routeName = 'api.'.$nameModule.'.tree-create';
    $r = Route2::get('/tree-create', [
        $cls, 'tree_create',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Tạo QuizTest tree';

    $routeName = 'api.'.$nameModule.'.tree-rename';
    $r = Route2::get('/tree-rename', [
        $cls, 'tree_save',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Rename QuizTest tree';

    $routeName = 'api.'.$nameModule.'.tree-delete';
    $r = Route2::get('/tree-delete', [
        $cls, 'tree_delete',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Delete QuizTest tree';

    $routeName = 'api.'.$nameModule.'.tree-move';
    $r = Route2::get('/tree-move', [
        $cls, 'tree_move',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Move on QuizTest tree';

});
