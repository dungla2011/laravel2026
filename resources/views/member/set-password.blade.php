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
    <style>
        .btn-glx {
            border: 1px solid #ccc;
            padding: 5px 10px;
        }

        td {
            padding: 5px;
        }

        input {
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
@endsection

@section('js')
    <script src="/admins/table_mng.js"></script>
    <script src="/vendor/div_table2/div_table2.js"></script>
    <script src="/admins/meta-data-table/meta-data-table.js"></script>

    <script>
        $("#btn-show-token").on('click', function () {
            $("#user_token").toggle();
        })
    </script>
@endsection

@section("content")


    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Member</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item" ppp590374958><a href="#"></a></li>
                            <li class="breadcrumb-item active"></li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <?php

                $user = \Illuminate\Support\Facades\Auth::user();

                if (!$user) {
                    bl("<a href='/login'> Đăng nhập để tiếp tục</a>");
                    goto  _END;
                }

                ?>
                <div class="row">
                    <div class="col-sm-6">
                        @if($errors->any())
                            <div class="jumbotron p-1 mt-1 mb-1" style="max-width: 600px; margin: 0 auto">
                                @foreach ($errors->all() as $error)
                                    <span class="text-danger">- {{ $error }}</span>
                                    <br>
                                @endforeach
                            </div>
                        @endif

                        <form action="" method="post">
                            @csrf
                            <table class="">
                                <tbody>
                                <tr>
                                    <td>Mậu khẩu cũ</td>
                                    <td><input type="password" class="" name="password">
                                        <a style="font-size: small; font-style: italic" href="/reset-password"> Quên mật
                                            khẩu </a>
                                        <div data-lastpass-icon-root="true"
                                             style="position: relative !important; height: 0px !important; width: 0px !important; float: left !important;"></div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Đặt mật khẩu mới:
                                    </td>
                                    <td><input type="password" class="" name="password1">
                                        <div data-lastpass-icon-root="true"
                                             style="position: relative !important; height: 0px !important; width: 0px !important; float: left !important;"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Nhập lại mật khẩu:
                                    </td>
                                    <td><input type="password" class="" name="password2">
                                        <div data-lastpass-icon-root="true"
                                             style="position: relative !important; height: 0px !important; width: 0px !important; float: left !important;"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                    </td>
                                    <td>
                                        <button type="submit" class="btn btn-glx">
                                            Đặt mật khẩu:
                                        </button>
                                        <a href="/member">
                                            <button type="button" class="btn btn-glx">
                                                Trở lại
                                            </button>
                                        </a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>

                    <div class="col-sm-6">
                    </div>
                </div>
                <br>

                <?php
                _END:
                ?>
            </div>
        </section>
        <!-- /.content -->
    </div>

@endsection
