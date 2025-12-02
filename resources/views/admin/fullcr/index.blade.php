<?php

use LadLib\Common\Database\MetaClassCommon;

?>

@extends("layouts.adm")

@section("title")
    Admin Index
@endsection

@section('title_nav_bar')
    ADMIN  1K
@endsection

@section('header')
    @include('parts.header-all')
@endsection

@section('css')
    <link rel="stylesheet" href="/vendor/div_table2/div_table2.css?v=<?php echo filemtime(public_path().'/vendor/div_table2/div_table2.css'); ?>">
    <link rel="stylesheet" href="/admins/table_mng.css?v=<?php echo filemtime(public_path().'/admins/table_mng.css'); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Outlined"
          rel="stylesheet">
@endsection

@section('js')
    <script src="/admins/table_mng.js"></script>
    <script src="/vendor/div_table2/div_table2.js"></script>
    <script src="/admins/meta-data-table/meta-data-table.js"></script>

@endsection

@section("content")

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <!-- Main content -->
        <section class="content">

            <div class="container-fluid pt-4">
                <span class="float-right" style="float: right">
                    <a target="_blank" href="/">
                        <i class="far fa-hand-grab-o"></i>
                        <a style="color: red" target="_blank" href="#">
                            <i class="fa fa-book"></i>
                            <i>
                        Hướng dẫn sử dụng

                            </a>
                    </a>
                </span>
                <b class="mb-3">

                    <i class="fas fa-calendar-alt"></i>
                    <a href="/tool1/_site/order_info_1k4s.php?get_all=1">
                    Tổng hợp
                    </a>
                </b>

                <div class="row pt-1">



                </div>

            </div>

            <div class="container-fluid">

            </div>

        </section>
        <!-- /.content -->
    </div>
@endsection
