<?php
use LadLib\Common\Database\MetaClassCommon;
?>
@extends("layouts.member")

@section("title")
 Member @endsection

@section('header')
    @include('parts.header-all')
@endsection

@section('css')
    <link rel="stylesheet" href="/vendor/div_table2/div_table2.css?v=<?php echo filemtime(public_path().'/vendor/div_table2/div_table2.css'); ?>">
    <link rel="stylesheet" href="/admins/table_mng.css?v=<?php echo filemtime(public_path().'/admins/table_mng.css'); ?>">
@endsection

@section('js')
    <script src="/admins/table_mng.js"></script>
    <script src="/vendor/div_table2/div_table2.js"></script>
    <script src="/admins/meta-data-table/meta-data-table.js"></script>

    <script>
        $("#btn-show-token").on('click', function (){
            $("#user_token").toggle();
        })
    </script>
@endsection

@section("content")
    <?php

        $user = \Illuminate\Support\Facades\Auth::user();

    ?>

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Member</h1>
                    </div><!-- /.col -->
{{--                    <div class="col-sm-6">--}}
{{--                        <ol class="breadcrumb float-sm-right">--}}
{{--                            <li class="breadcrumb-item" ppp084590374958><a href="#"></a></li>--}}
{{--                            <li class="breadcrumb-item active"></li>--}}
{{--                        </ol>--}}
{{--                    </div><!-- /.col -->--}}
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-3">
                        Mã Tài khoản: <?php
                        echo \App\Components\ClassRandId2::getRandFromId(getUserIdCurrent_());
                        if(\App\Models\User::isSupperAdmin()){
                            echo " <span style='color: transparent'> [" . getUserIdCurrent_() .'] </span>';
                        }
                        ?>
                    </div>
                    <div class="col-sm-3">
                        <span>
                            <?php
                            echo $user->email ;
                            if(!$user->password)
                                echo "<br/>\n <a href='/reset-password'> Đặt mật khẩu </a>";
                            else
                                echo "<br/>\n <a href='/member/set-password'> Đặt mật khẩu </a>";
                            ?>
                        </span>
                    </div>
                    <div class="col-sm-6">
                        <button id="btn-show-token" style="display: inline-block" type="button" class="btn btn-default">Get Api Token</button>
                        <span id="user_token" style="display: none">
                            <input readonly style="" type="text" class="form-control" value="<?php
                            echo Auth()->user()->getJWTUserToken() ;
                            ?>">
                            <?php
                            ?>
                        </span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12 jumbotron" style="text-align: center; background-color: white; margin-top: 30px">

                        <b style="font-size: large">
                        <a href="/member/network-marketing/shb">

                            <img src="/images/net-mar.jpg" alt="">
                            <br>
                            <br>Truy cập Hệ thống network Marketting của bạn </a>
                        </b>
                        <br>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
@endsection
