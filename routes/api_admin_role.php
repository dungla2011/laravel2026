<?php

use App\Components\Route2;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route2::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route2::prefix('/admin-role')->group(function () {
    $route_group_desc = 'API - Save Meta Data';

    $nameModule = 'admin-role';
    $cls = \App\Http\ControllerApi\CommonControllerApi::class;
    $routeName = 'api.'.$nameModule.'.save-role';
    $r = Route2::match(['GET', 'POST'], '/save-role', [
        $cls, 'saveRole',
    ])->name($routeName);
    if ($r instanceof Route2);
    $r->middleware('can:'.$routeName);
    $r->route_group_desc_ = $route_group_desc;
    $r->route_desc_ = 'Save Meta Data';
});
