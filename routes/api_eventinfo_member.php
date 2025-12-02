<?php

use App\Components\Route2;

Route2::prefix('/member-event-info')->group(function () {

    $route_group_desc = 'API - Thao tác với EventInfo';
    $nameModule = 'member-event-info';
    $modelUsing_ = \App\Models\EventInfo::class;

    $cls = \App\Http\ControllerApi\EventInfoControllerApi::class;

    ////////////////////////////////////////////////////////////
    //New ------------


    $routeName = 'api.'.$nameModule.'.saveSignatureUser';
    $r = Route2::post('/saveSignatureUser', [
        \App\Http\ControllerApi\EventInfoControllerApi::class, 'saveSignatureUser',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'saveSignatureUser user to event';
    $r->modelUsing_ = $modelUsing_;


    $routeName = 'api.'.$nameModule.'.sendRegConfirmMail';
    $r = Route2::get('/sendRegConfirmMail', [
        \App\Http\ControllerApi\EventInfoControllerApi::class, 'sendRegConfirmMail',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'sendRegConfirmMail user to event';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.importExcelUserEvent';
    $r = Route2::post('/importExcelUserEvent', [
        \App\Http\ControllerApi\EventInfoControllerApi::class, 'importExcelUserEvent',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Add user to event';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.approvePublicUser';
    $r = Route2::get('/approvePublicUser', [
        \App\Http\ControllerApi\EventInfoControllerApi::class, 'approvePublicUser',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Add user to event';
    $r->modelUsing_ = $modelUsing_;


    $routeName = 'api.'.$nameModule.'.addUserToEvent';
    $r = Route2::post('/addUserToEvent', [
        \App\Http\ControllerApi\EventInfoControllerApi::class, 'addUserToEvent',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Add user to event';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.saveEventChannel';
    $r = Route2::post('/saveEventChannel', [
        \App\Http\ControllerApi\EventInfoControllerApi::class, 'saveEventChannel',
    ])->name($routeName);
    if ($r instanceof Route2);
    //    $r->middleware("can:" . $routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'saveEventChanel';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.sendMailTest';
    $r = Route2::post('/sendMailTest', [
        \App\Http\ControllerApi\EventInfoControllerApi::class, 'sendMailTest',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Add user to event';
    $r->modelUsing_ = $modelUsing_;

    //sendTinAll
    $routeName = 'api.'.$nameModule.'.sendTinAll';
    $r = Route2::post('/sendTinAll', [
        \App\Http\ControllerApi\EventInfoControllerApi::class, 'sendTinAll',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Send tin all';
    $r->modelUsing_ = $modelUsing_;

    //syncSms
    $routeName = 'api.'.$nameModule.'.syncSms';
    $r = Route2::post('/syncSms', [
        \App\Http\ControllerApi\EventInfoControllerApi::class, 'syncSms',
    ])->name($routeName);
    if ($r instanceof Route2);
    //    $r->middleware("can:" . $routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'syncSms';
    $r->modelUsing_ = $modelUsing_;


    $routeName = 'api.'.$nameModule.'.syncSms2';
    $r = Route2::post('/syncSms2', [
        \App\Http\ControllerApi\EventInfoControllerApi::class, 'syncSms2',
    ])->name($routeName);
    if ($r instanceof Route2);
    //    $r->middleware("can:" . $routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'syncSms2';
    $r->modelUsing_ = $modelUsing_;


    //syncSms
    $routeName = 'api.'.$nameModule.'.syncSmsGet';
    $r = Route2::get('/syncSms', [
        $cls, 'syncSms',
    ])->name($routeName);
    if ($r instanceof Route2);
    //    $r->middleware("can:" . $routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'syncSmsGet';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.stopSendTinAll';
    $r = Route2::post('/stopSendTinAll', [
        $cls, 'stopSendTinAll',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Stop send tin all';
    $r->modelUsing_ = $modelUsing_;

    /////////ORG
    $routeName = 'api.'.$nameModule.'.list';
    $r = Route2::get('/list', [
        $cls, 'list',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Xem danh sách event-info';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.get';
    $r = Route2::get('/get/{id}', [
        $cls, 'get',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Lấy thông tin event-info';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.add';
    $r = Route2::post('/add', [
        $cls, 'add',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Thêm event-info';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.update';
    $r = Route2::post('/update/{id}', [
        $cls, 'update',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Cập nhật event-info';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.update-multi';
    $r = Route2::post('/update-multi', [
        $cls, 'update_multi',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Cập nhật event-info - multi';
    $r->modelUsing_ = $modelUsing_;
    $r->showApi_ = 0;
    //
    $routeName = 'api.'.$nameModule.'.delete';
    $r = Route2::get('/delete', [
        $cls, 'delete',
    ])->name($routeName)->where('name', '.*');
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Xóa event-info';
    $r->modelUsing_ = $modelUsing_;

    $routeName = 'api.'.$nameModule.'.undelete';
    $r = Route2::get('/un-delete', [
        $cls, 'un_delete',
    ])->name($routeName)->where('name', '.*');
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Khôi phục event-info';
    $r->modelUsing_ = $modelUsing_;

    ////FOR TREE - NẾU CÓ //////////////////////////////////////////////
    $routeName = 'api.'.$nameModule.'.tree-index';
    $r = Route2::get('/tree', [
        $cls, 'tree_index',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Tree list EventInfo';

    $routeName = 'api.'.$nameModule.'.tree-create';
    $r = Route2::get('/tree-create', [
        $cls, 'tree_create',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Tạo EventInfo tree';

    $routeName = 'api.'.$nameModule.'.tree-rename';
    $r = Route2::get('/tree-rename', [
        $cls, 'tree_save',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Rename EventInfo tree';

    $routeName = 'api.'.$nameModule.'.tree-delete';
    $r = Route2::get('/tree-delete', [
        $cls, 'tree_delete',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Delete EventInfo tree';

    $routeName = 'api.'.$nameModule.'.tree-move';
    $r = Route2::get('/tree-move', [
        $cls, 'tree_move',
    ])->name($routeName);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Move on EventInfo tree';

});
