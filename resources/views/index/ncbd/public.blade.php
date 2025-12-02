@extends(getLayoutNameMultiReturnDefaultIfNull())

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
    .top-nav-zone{
        position: relative;
    }
    .heading1 a {
        color: white;
    }
    footer {
        position: relative!important;
    }
</style>

@section('title')
    <?php
    echo \App\Models\SiteMng::getTitle();
    ?>

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



    <div class="container p-3 rounded text-center mt-3" style=" TEXT-ALIGN: CENTER; COLOR: dodgerblue; border-radius: 20px">

{{--        <img  style="width: 100%; max-width: 600px" src="/images/tmp/ev1.jpg" alt="">--}}
{{--        <img  style="width: 100%; max-width: 600px" src="/images/tmp/ev2.png" alt="">--}}

        <br>
        <H1 STYLE="text-shadow:
        10px 10px 10px #aaa; color: #0a6aa1">
            <b>
        DAV - LEADING INNOVATION
            </b>
        </H1>

        <img  style=" margin-top: 50px;
        box-shadow : 0px 0px 10px 5px #6f6d6dab;
        width: 100%; max-width: 1000px" src="/images/tmp/ev6.jpg" alt="">

        <br><br><br>


    </div>

    <p></p>
@endsection
