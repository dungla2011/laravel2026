<?php

use App\Components\Route2;

Route2::prefix('/download-file')->group(function () {

    $route_group_desc = 'API - Download File';
    $nameModule = 'DownloadFileAPI';
    $modelUsing_ = \App\Models\DownloadLog::class;

    $cls = \App\Http\ControllerApi\DownloadFileControllerApi::class;

    $routeName = 'api.'.$nameModule.'.getLinkDownload';
    $r = Route2::get('/getLinkDownload', [
        $cls, 'getLinkDownload',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'GetLinkDl';
    $r->modelUsing_ = $modelUsing_;

    ///////////////////////////////////////////////////////////

});
