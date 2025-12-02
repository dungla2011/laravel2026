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
    .heading1 a {
        color: white;
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

    <div class="container" style="min-height: 500px">


        <div class="jumbotron">

            <div class="my-4 p-5 bg-primary text-white rounded">
                1k/1 link

                <br>

                <br>
                <br>
                <br>
                Nạp 10k tối thiểu
            </div>



        </div>


    </div>

@endsection
