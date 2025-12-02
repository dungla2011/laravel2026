<?php

use App\Components\Route2;
use Illuminate\Support\Facades\Route;

Route2::prefix('/demo-folder-tree')->group(function () {

    //    $route_group_desc = "API - Thao tác với folder tree";
    //    $nameModule = 'demo-folder-tree';
    //
    //    $cls = \App\Http\ControllerApi\DemoFolderControllerApi::class;
    //
    //    $routeName = "api." . $nameModule . ".tree-index";
    //    $r = Route2::get("/list", [
    //        $cls, 'tree_index',
    //    ])->name($routeName);
    //    $r->middleware("can:" . $routeName);
    //    $r->route_group_desc_ = $route_group_desc;
    //    $r->route_desc_ = "Xem danh sách folder";
    //
    //    $routeName = "api." . $nameModule . ".tree-create";
    //    $r = Route2::get("/create", [
    //        $cls, 'tree_create',
    //    ])->name($routeName);
    //    $r->middleware("can:" . $routeName);
    //    $r->route_group_desc_ = $route_group_desc;
    //    $r->route_desc_ = "Tạo folder tree";
    //
    //    $routeName = "api." . $nameModule . ".tree-rename";
    //    $r = Route2::get("/rename", [
    //        $cls, 'tree_save',
    //    ])->name($routeName);
    //    $r->middleware("can:" . $routeName);
    //    $r->route_group_desc_ = $route_group_desc;
    //    $r->route_desc_ = "Rename Folder tree";
    //
    //    $routeName = "api." . $nameModule . ".tree-delete";
    //    $r = Route2::get("/delete", [
    //        $cls, 'tree_delete',
    //    ])->name($routeName);
    //    $r->middleware("can:" . $routeName);
    //    $r->route_group_desc_ = $route_group_desc;
    //    $r->route_desc_ = "Delete Folder tree";
    //
    //    $routeName = "api." . $nameModule . ".tree-move";
    //    $r = Route2::get("/move", [
    //        $cls, 'tree_move',
    //    ])->name($routeName);
    //    $r->middleware("can:" . $routeName);
    //    $r->route_group_desc_ = $route_group_desc;
    //    $r->route_desc_ = "Move on tree";

});
