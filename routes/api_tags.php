<?php

use App\Components\Route2;

Route2::prefix('/tag-demo')->group(function () {

    $route_group_desc = 'API - Tags';
    $routeName = 'api.tag-demo.search';
    $r = Route2::match(['GET', 'POST'], 'search', [
        \App\Http\ControllerApi\TagsDemoControllerApi::class, 'search',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->modelUsing_ = \App\Models\TagDemo::class;
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Search Tags';
    $r->showApi_ = 1;
    $r->docs_ = "
    * @apiParam {String} field=name
    * @apiParam {String} search_str Tên tag muốn tìm
    * @apiExample:
    * Post to api: [field=>'name', search_str='abc'];
    ";

});
