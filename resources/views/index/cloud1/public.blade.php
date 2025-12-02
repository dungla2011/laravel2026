@extends(getLayoutNameMultiReturnDefaultIfNull())


@section('css')

    <style>
        .row1 {
            border-bottom: 2px solid darkorange;
        }

        .heading1 {
            background-color: darkorange;
            color: white;
            display: inline-block;
            font-weight: bold;
            padding: 7px 30px 7px 15px;
            font-size: 20px;
            text-transform: uppercase;
        }
        .heading1 a {
            color: white;
        }
        .cls1 li::after {
            content: '-';
            color: transparent;
        }
    </style>

@endsection
@section('title')
    <?php
//    echo \App\Models\SiteMng::getTitle();
    ?>
    ZCloud.io.vn : File cloud management system

@endsection

@section('meta-description')<?php
    echo \App\Models\SiteMng::getDesc()
    ?>
@endsection

@section('meta-keywords')<?php
    echo \App\Models\SiteMng::getKeyword()
    ?>
@endsection

@section('content')



    <div class="container p-3 rounded text-center mt-5" style=" min-height: 500px; TEXT-ALIGN: CENTER; COLOR: royalblue; border-radius: 20px">

{{--        <img  style="width: 100%; max-width: 600px" src="/images/tmp/ev1.jpg" alt="">--}}
{{--        <img  style="width: 100%; max-width: 600px" src="/images/tmp/ev2.png" alt="">--}}

        <h2>
            DEMO - FILE CLOUD
        </h2>

        <p></p>
        <h2><a href="/login"> Đăng nhập </a></h2>

    </div>

    <p></p>
@endsection
