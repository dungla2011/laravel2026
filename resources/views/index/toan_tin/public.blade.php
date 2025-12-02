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

    .blink1 {
        animation: blink1 1s infinite;
    }
    @keyframes blink1 {
        0%, 50% {
            opacity: 1;
        }
        50.01%, 100% {
            opacity: 0;
        }
    }
</style>
@endsection
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


    <div class="slide-home" style=" display: none">

        <div class="slogan" style="">
            "Toán học là cánh cửa và là chìa khoá để đi vào các ngành khoa học khác"
            <br>

            <i>- Roger Bacon - </i>

        </div>

    </div>

{{--    <div style="text-align: right; padding: 10px; border: 0px solid #ccc; margin-top: 10px"><a class="blink1" href="https://zalo.me/g/gwsbie344"> <img style="height: 30px" src="/images/icon/zalo.png" alt="">  Nhóm Zalo Hỗ trợ Siêu Toán Tiểu học </a> </div>--}}

    <?php

        $mCat = \App\Models\MyDocumentCat::where(['parent_id'=> 27])->orderBy('orders', 'asc')->get();
        foreach ($mCat AS $cat){
            ?>

                <div data-code-pos="qqq1706685249431" class="container pt-4">
                    <div class="row1">
                        <div class="heading1">
                            <a href="/tai-lieu/danh-muc?fid={{$cat->id}}"> {{$cat->name}}</a>
                        </div>
                        <div class="" style="float: right"><a href="/tai-lieu/danh-muc?fid={{$cat->id}}"> <i class="fa fa-caret-right"></i> Xem tất cả </a> </div>
                    </div>
                    <br>

                    <div class="row row-cols-2 row-cols-md-5 g-4">

                        <?php

                        $mDoc = \App\Models\MyDocument::where(['parent_id'=> $cat->id])->whereNotNull('file_list')->where(function ($query) {
//                            $query->where('name', 'like', "%toán%")->orWhere('name', 'like', "%tin học%");
                        } )->limit(5)->get();

                        $dmx = 0;
                        foreach ($mDoc AS $doc){
                            $dmx++;
                            if($dmx > 5)
                                $dmx = 1;
                            if($doc instanceof \App\Models\ModelGlxBase);
                            $img = $doc->getThumbSmall(300, 'https://toantin'.$dmx.".mytree.vn");
                        ?>

                        <div class="col d-flex align-items-stretch">
                            <div class="card">
                                <a href="/tai-lieu/chi-tiet?fid={{qqgetRandFromId_($doc->getId())}}">
                                    <img src="{{$img}}" class="placeholder1 card-img-top"
                                         alt="{{$doc->name}}"/>
                                    <div class="card-body">
                                        <h5 class="card-title">{{$doc->name}}</h5>
                                        <p class="card-text">

                                        </p>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <?php
                        }
                        ?>

                    </div>

                </div>


    <?php

        }

    ?>


    <style>
        .new-left{
            width: 30%; border: 0px solid #ccc; display: inline-block; float: left;
            margin-bottom: 15px;
        }
        .new-right{
            width: 69%; border: 0px solid #ccc; display: inline-block; float: left;
            padding: 10px;
        }
    </style>

    <div class="container pt-5 d-none">
        <div class="row1">
            <div class="heading1">Tin tức</div>
        </div>
        <br>

        <div class="row mb-2">

            <div class="col-md-6">
                <div style="" class="new-left clearfix">
                    <img src="/images/tmp/toan12-2.jpg" style="width: 100%" alt="">
                </div>
                <div style="" class="new-right clearfix">
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Neque sit aut excepturi laudantium ipsa assumenda, maxime beatae quos commodi in nihil quas earum dignissimos iste, fuga pariatur culpa est corrupti.
                </div>
            </div>

            <div class="col-md-6">
                <div style="" class="new-left clearfix">
                    <img src="/images/tmp/toan12-2.jpg" style="width: 100%" alt="">
                </div>
                <div style="" class="new-right clearfix">
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Neque sit aut excepturi laudantium ipsa assumenda, maxime beatae quos commodi in nihil quas earum dignissimos iste, fuga pariatur culpa est corrupti.
                </div>
            </div>

            <div class="col-md-6">
                <div style="" class="new-left clearfix">
                    <img src="/images/tmp/toan12-2.jpg" style="width: 100%" alt="">
                </div>
                <div style="" class="new-right clearfix">
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Neque sit aut excepturi laudantium ipsa assumenda, maxime beatae quos commodi in nihil quas earum dignissimos iste, fuga pariatur culpa est corrupti.
                </div>
            </div>

            <div class="col-md-6">
                <div style="" class="new-left clearfix">
                    <img src="/images/tmp/toan12-2.jpg" style="width: 100%" alt="">
                </div>
                <div style="" class="new-right clearfix">
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Neque sit aut excepturi laudantium ipsa assumenda, maxime beatae quos commodi in nihil quas earum dignissimos iste, fuga pariatur culpa est corrupti.
                </div>
            </div>

            <div class="col-md-6">
                <div style="" class="new-left clearfix">
                    <img src="/images/tmp/toan12-2.jpg" style="width: 100%" alt="">
                </div>
                <div style="" class="new-right clearfix">
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Neque sit aut excepturi laudantium ipsa assumenda, maxime beatae quos commodi in nihil quas earum dignissimos iste, fuga pariatur culpa est corrupti.
                </div>
            </div>

            <div class="col-md-6">
                <div style="" class="new-left clearfix">
                    <img src="/images/tmp/toan12-2.jpg" style="width: 100%" alt="">
                </div>
                <div style="" class="new-right clearfix">
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Neque sit aut excepturi laudantium ipsa assumenda, maxime beatae quos commodi in nihil quas earum dignissimos iste, fuga pariatur culpa est corrupti.
                </div>
            </div>




        </div>

    </div>

    <p></p>
@endsection
