<?php
if($fid = request('id'))
{    $folder = \App\Models\MediaFolder::find($fid);
    if(!$folder){
        //Chuyển sang 404
        return abort(404);
        die("Folder not found");
    }
}

?>
@extends(getLayoutNameMultiReturnDefaultIfNull())

@section('title')
    <?php
    if($folder ?? '')
        echo "Thể loại: " . $folder->name;
    else
        echo "Tất cả Thể loại";
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


    <!-- page title -->
    <section class="section section--first"  style="display: none;" data-code-pos='ppp17458365198371'>
        <div class="container"  style=" ">
            <div class="row">
                <div class="col-12">
                    <div class="section__wrap" data-code-pos='ppp17458365224681'>
                        <!-- section title -->
{{--                        <h1 class="section__title section__title--head">Catalog</h1>--}}
                        <!-- end section title -->

                        <!-- breadcrumbs -->
                        <ul class="breadcrumbs">

                            <?php
                            $fid = request('id');
                            if($fid){
                                $folder = \App\Models\MediaFolder::find($fid);
                                if($folder instanceof \App\Models\MediaFolder);
                                $str = $folder->getBreakumPathHtml();
                                echo "<a href='/movie'>  Thể loại :: </a> <span style='color: white; margin: 0px 5px;'>  </span> $str";
                            }
                            else{
                                echo "<a href='/movie'>  Thể loại :: </a>";
                            }
                            ?>
                        </ul>
                        <!-- end breadcrumbs -->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end page title -->

    <!-- filter -->
    <div class="filter" style="margin-top: 80px">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="filter__content">
                        <!-- menu btn -->
                        <button class="filter__menu" type="button"><i class="ti ti-filter"></i>Filter</button>
                        <!-- end menu btn -->

                        <!-- filter desk -->
                        <div class="filter__items">
                            <select class="filter__select" name="genre" id="filter__genre">
                                <option value="0">All genres</option>
                                <?php
                                $mm = \App\Models\MediaFolder::where('parent_id', 0)->orderBy('name', 'asc')->get();
                                foreach ($mm as $m) {
                                    $id = $m->id;
                                    $name = $m->name;
                                    echo "<option value='$id'>$name</option>";
                                }
                                ?>

                            </select>

                            <select class="filter__select" name="quality" id="filter__quality">
                                <option value="0">Any quality</option>
                                <option value="1">HD 1080</option>
                                <option value="2">HD 720</option>
                                <option value="3">DVD</option>
                                <option value="4">TS</option>
                            </select>

                            <select class="filter__select" name="rate" id="filter__rate">
                                <option value="0">Any rating</option>
                                <option value="1">from 3.0</option>
                                <option value="2">from 5.0</option>
                                <option value="3">from 7.0</option>
                                <option value="4">Golder Star</option>
                            </select>

                            <select class="filter__select" name="sort" id="filter__sort">
                                <option value="0">Relevance</option>
                                <option value="1">Newest</option>
                                <option value="2">Oldest</option>
                            </select>
                        </div>
                        <!-- end filter desk -->

                        <!-- filter btn -->
                        <button class="filter__btn" type="button">Apply</button>
                        <!-- end filter btn -->

                        <!-- amount -->
                        <span class="filter__amount">Showing 18 of 1713</span>
                        <!-- end amount -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end filter -->

    <!-- mobile filter -->
    <div class="mfilter">
        <div class="mfilter__head">
            <h6 class="mfilter__title">Filter</h6>

            <button class="mfilter__close" type="button"><i class="ti ti-x"></i></button>
        </div>

        <div class="mfilter__select-wrap">
            <div class="sign__group">
                <select class="filter__select" name="mgenre" id="mfilter__genre">
                    <option value="0">All genres</option>
                    <?php
                    $mm = \App\Models\MediaFolder::where('parent_id', 0)->orderBy('name', 'asc')->get();
                    foreach ($mm as $m) {
                        $id = $m->id;
                        $name = $m->name;
                        echo "<option value='$id'>$name</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="sign__group">
                <select class="filter__select" name="mquality" id="mfilter__quality">
                    <option value="0">Any quality</option>
                    <option value="1">HD 1080</option>
                    <option value="2">HD 720</option>
                    <option value="3">DVD</option>
                    <option value="4">TS</option>
                </select>
            </div>

            <div class="sign__group">
                <select class="filter__select" name="mrate" id="mfilter__rate">
                    <option value="0">Any rating</option>
                    <option value="1">from 3.0</option>
                    <option value="2">from 5.0</option>
                    <option value="3">from 7.0</option>
                    <option value="4">Golder Star</option>
                </select>
            </div>

            <div class="sign__group">
                <select class="filter__select" name="msort" id="mfilter__sort">
                    <option value="0">Relevance</option>
                    <option value="1">Newest</option>
                    <option value="2">Oldest</option>
                </select>
            </div>
        </div>

        <button class="mfilter__apply" type="button">Apply</button>
    </div>
    <!-- end mobile filter -->

    <!-- catalog -->
    <div class="section section--catalog">
        <div class="container">
            <div class="row">

                <?php

                    if($fid){
                        //Lâấy ra các MediaItem, có pivot chứa id của folder
                        $items = \App\Models\MediaItem::whereHas('_folders', function ($query) use ($fid) {
                            $query->where('media_folder_id', $fid);
                        })->paginate(18);
                    }else{
                        //Lâý ra các MediaItem, có pivot chứa id của folder
                        $items = \App\Models\MediaItem::paginate(18);
                    }


                foreach ($items as $item){
                 $link1 = $item->getLink1();
                ?>
                <!-- item -->
                <div class="col-6 col-sm-4 col-lg-3 col-xl-2">
                    <div class="item">
                        <div class="item__cover">
                            <img src="{{$item->thumb}}" alt="">
                            <a href="{{$link1}}" class="item__play">
                                <i class="ti ti-player-play-filled"></i>
                            </a>

                            <span class="item__rate item__rate--green"> {{ (80 + $item->id % 20) / 10  }}</span>

                            <button class="item__favorite" type="button"><i class="ti ti-bookmark"></i></button>
                        </div>
                        <div class="item__content">
                            <h3 class="item__title">
                                <a href="{{$link1}}"> {{$item->name}} </a>
                            <span class="item__category">

                                @foreach($item->_folders->take(2) AS $folder)
                                    <a href="{{$folder->getLink1()}}">{{$folder->name}}</a>
                                @endforeach
                            </span>
                        </div>
                    </div>
                </div>
                <?php
                }
                ?>
                <!-- end item -->

            </div>

            <div class="row">
                <!-- paginator -->
                <div class="col-12">
                    <!-- paginator mobile -->
                    <div class="paginator-mob">
                        <span class="paginator-mob__pages">18 of 1713</span>

                        <ul class="paginator-mob__nav">
                            <li>
                                <a href="#">
                                    <i class="ti ti-chevron-left"></i>
                                    <span>Prev</span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <span>Next</span>
                                    <i class="ti ti-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <!-- end paginator mobile -->

                    <!-- paginator desktop -->
                    <?php

                        if($fid){
                            $tt = \App\Models\MediaItem::whereHas('_folders', function ($query) use ($fid) {
                                $query->where('media_folder_id', $fid);
                            })->count();
                        }
                        else
                            $tt = \App\Models\MediaItem::where('parent_id', $fid)->count();

                        echo \LadLib\Common\clsPaginator2::showPaginatorBasicStyleULLI(null,$tt, 20,0,5,'paginator','paginator__item','paginator__item--active','', '' )

                    ?>
                    <!-- end paginator desktop -->
                </div>
                <!-- end paginator -->
            </div>
        </div>
    </div>
    <!-- end catalog -->

    <!-- section -->

    <!-- section -->
    <section class="section section--border">
        <div class="container">
            <div class="row">
                <!-- section title -->
                <div class="col-12">
                    <div class="section__title-wrap">
                        <h2 class="section__title">Phim xem nhiều</h2>
                        <a href="/movie" class="section__view section__view--carousel">View All</a>
                    </div>
                </div>
                <!-- end section title -->

                <!-- carousel -->
                <div class="col-12">
                    <div class="section__carousel splide splide--content">
                        <div class="splide__arrows">
                            <button class="splide__arrow splide__arrow--prev" type="button">
                                <i class="ti ti-chevron-left"></i>
                            </button>
                            <button class="splide__arrow splide__arrow--next" type="button">
                                <i class="ti ti-chevron-right"></i>
                            </button>
                        </div>

                        <div class="splide__track">
                            <ul class="splide__list">

                                <?php

                                $mTop = \App\Models\MediaItem::where('options', 2)->take(10)->get();

                                ?>
                                @foreach($mTop AS $one)
                                    <li class="splide__slide">
                                        <div class="item item--carousel">
                                            <div class="item__cover">
                                                <img src="{{$one->thumb}}" alt="">
                                                <a href="{{$one->getLink1()}}" class="item__play">
                                                    <i class="ti ti-player-play-filled"></i>
                                                </a>
                                                <span class="item__rate item__rate--green">
                                                {{ (80 + $one->id % 20) / 10}}

                                            </span>
                                                <button class="item__favorite" type="button"><i class="ti ti-bookmark"></i></button>
                                            </div>
                                            <div class="item__content">
                                                <h3 class="item__title"><a href="{{$one->getLink1()}}"> {{$one->name}}</a></h3>
                                                <span class="item__category">
                                                    @foreach($one->_folders->take(2) AS $folder)
                                                        <a href="{{$folder->getLink1()}}">{{$folder->name}}</a>
                                                    @endforeach

											</span>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach

                            </ul>
                        </div>
                    </div>
                </div>
                <!-- end carousel -->
            </div>
        </div>
    </section>
    <!-- end section -->

    <!-- end section -->



@endsection
