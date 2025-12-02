@extends(getLayoutNameMultiReturnDefaultIfNull())

@section('title')
    <?php
    echo \App\Models\SiteMng::getTitle();
    ?>

@endsection

@section('description')<?php
    echo \App\Models\SiteMng::getDesc()
    ?>
@endsection

@section('keywords')<?php
    echo \App\Models\SiteMng::getKeyword()
    ?>
@endsection

@section('content')


    <!-- home -->
    <section class="home home--bg">
        <div class="container">
            <div class="row">
                <!-- home title -->
                <div class="col-12" data-code-pos='ppp17458882500451'>
                    <h1 class="home__title">TOP PHIM</h1>
                </div>
                <!-- end home title -->

                <!-- home carousel -->
                <div class="col-12" data-code-pos='ppp17458882537111'>
                    <div class="home__carousel splide splide--home">
                        <div class="splide__arrows">
                            <button class="splide__arrow splide__arrow--prev" type="button">
                                <i class="ti ti-chevron-left"></i>
                            </button>
                            <button class="splide__arrow splide__arrow--next" type="button">
                                <i class="ti ti-chevron-right"></i>
                            </button>
                        </div>

                        <div class="splide__track">
                            <ul class="splide__list" data-code-pos='ppp17458882578971'>

                                <?php
                                $mTop = \App\Models\MediaItem::where('options', 1)->take(10)->get();
                                ?>

                                @foreach($mTop AS $one)

                                <li class="splide__slide" data-code-pos='ppp17458882615221'>
                                    <div class="item item--hero">
                                        <div class="item__cover">
                                            <img src="{{$one->thumb}}" alt="">
                                            <a href="{{$one->getLink1()}}" class="item__play">
                                                <i class="ti ti-player-play-filled"></i>
                                            </a>
                                            <span class="item__rate item__rate--green"> {{ (80 + $one->id % 20) / 10  }}</span>
                                            <button class="item__favorite" type="button"><i class="ti ti-bookmark"></i></button>
                                        </div>
                                        <div class="item__content">
                                            <h3 class="item__title"><a href="{{$one->getLink1()}}">{{$one->name}}</a></h3>
                                            <span class="item__category">
                                                @foreach($one->_folders->take(2) AS $folder)
                                                        <a href="/the-loai/{{$folder->getLink1()}}">{{$folder->name}}</a>
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
                <!-- end home carousel -->
            </div>
        </div>
    </section>
    <!-- end home -->

    <!-- filter -->
    <div class="filter" data-code-pos='ppp17458882656821'>
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
                                <option value="0">Thể loại</option>
                                <?php
                                $mfold = \App\Models\MediaFolder::where('status' ,'>', 0)->orderBy('name', 'asc')->get();
                                foreach ( $mfold AS $mf){
                                    //Slug name: $mf->name:
                                    $slg = \Illuminate\Support\Str::slug($mf->name);
                                    $lk = $mf->getLink1();
                                    ?>
                                <option value="{{$mf->id}}">{{$mf->name}}</option>
                                    <?php
                                }
                                ?>
                            </select>

                            <select class="filter__select" name="quality" id="filter__quality">
                                <option value="0">Chất lượng</option>
                                <option value="1">HD 1080</option>
                                <option value="2">HD 720</option>
                                <option value="3">DVD</option>
                                <option value="4">TS</option>
                            </select>

                            <select class="filter__select" name="rate" id="filter__rate">
                                <option value="0">Đánh giá IMDB</option>
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

    <!-- catalog -->
    <div class="section section--catalog">
        <div class="container">
            <div class="row">
                <!-- item -->

                <?php

                    $mm = \App\Models\MediaItem::orderBy('id', 'desc')->limit(24)->get();
                ?>

                @foreach($mm AS $obj)
                <div class="col-6 col-sm-4 col-lg-3 col-xl-2">
                    <div class="item">
                        <div class="item__cover">
                            <img src="{{$obj->thumb}}" alt="">
                            <a href="{{$obj->getLink1()}}" class="item__play">
                                <i class="ti ti-player-play-filled"></i>
                            </a>
                            <span class="item__rate item__rate--green">
                            {{ (80 + $obj->id % 20) / 10}}
                            </span>

                            <button class="item__favorite" type="button"><i class="ti ti-bookmark"></i></button>
                        </div>
                        <div class="item__content">
                            <h3 class="item__title"><a href="{{$obj->getLink1()}}">{{$obj->name}}</a></h3>
                            <span class="item__category">
                            @foreach($obj->_folders->take(2) AS $folder)
                                    <a href="{{$folder->getLink1()}}">{{$folder->name}}</a>
                                @endforeach
							</span>
                        </div>
                    </div>
                </div>
                @endforeach
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

                    <?php

                        $tt = \App\Models\MediaItem::count();
                        echo \LadLib\Common\clsPaginator2::showPaginatorBasicStyleULLI("/movie",$tt, 20,0,5,'paginator','paginator__item','paginator__item--active','', '' )

                    ?>
                    <!-- paginator desktop -->

                    <!-- end paginator desktop -->
                </div>
                <!-- end paginator -->
            </div>
        </div>
    </div>
    <!-- end catalog -->

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

    <!-- section -->
    <section class="section section--border">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="section__title">Select your plan</h2>
                </div>
            </div>

            <div class="row">
                <!-- plan -->
                <div class="col-12 col-md-6 col-lg-4 order-md-2 order-lg-1">
                    <div class="plan">
                        <h3 class="plan__title">Basic</h3>
                        <span class="plan__price">Free</span>
                        <ul class="plan__list">
                            <li class="plan__item"><i class="ti ti-circle-check"></i> 7 days</li>
                            <li class="plan__item"><i class="ti ti-circle-check"></i> 720p Resolution</li>
                            <li class="plan__item plan__item--none"><i class="ti ti-circle-minus"></i> Limited Availability</li>
                            <li class="plan__item plan__item--none"><i class="ti ti-circle-minus"></i> Desktop Only</li>
                            <li class="plan__item plan__item--none"><i class="ti ti-circle-minus"></i> Limited Support</li>
                        </ul>
                        <a href="signin.html" class="plan__btn">Register</a>
                    </div>
                </div>
                <!-- end plan -->

                <!-- plan -->
                <div class="col-12 col-md-12 col-lg-4 order-md-1 order-lg-2">
                    <div class="plan plan--orange">
                        <h3 class="plan__title">Premium</h3>
                        <span class="plan__price">$34.99 <sub>/ month</sub></span>
                        <ul class="plan__list">
                            <li class="plan__item"><i class="ti ti-circle-check"></i> 1 Month</li>
                            <li class="plan__item"><i class="ti ti-circle-check"></i> Full HD</li>
                            <li class="plan__item"><i class="ti ti-circle-check"></i> Limited Availability</li>
                            <li class="plan__item plan__item--none"><i class="ti ti-circle-minus"></i> TV & Desktop</li>
                            <li class="plan__item plan__item--none"><i class="ti ti-circle-minus"></i> 24/7 Support</li>
                        </ul>
                        <button class="plan__btn" type="button" data-bs-toggle="modal" data-bs-target="#plan-modal">Choose Plan</button>
                    </div>
                </div>
                <!-- end plan -->

                <!-- plan -->
                <div class="col-12 col-md-6 col-lg-4 order-md-3">
                    <div class="plan plan--red">
                        <h3 class="plan__title">Cinematic</h3>
                        <span class="plan__price">$49.99 <sub>/ month</sub></span>
                        <ul class="plan__list">
                            <li class="plan__item"><i class="ti ti-circle-check"></i> 2 Months</li>
                            <li class="plan__item"><i class="ti ti-circle-check"></i> Ultra HD</li>
                            <li class="plan__item"><i class="ti ti-circle-check"></i> Limited Availability</li>
                            <li class="plan__item"><i class="ti ti-circle-check"></i> Any Device</li>
                            <li class="plan__item"><i class="ti ti-circle-check"></i> 24/7 Support</li>
                        </ul>
                        <button class="plan__btn" type="button" data-bs-toggle="modal" data-bs-target="#plan-modal">Choose Plan</button>
                    </div>
                </div>
                <!-- end plan -->
            </div>
        </div>
    </section>
    <!-- end section -->


@endsection
