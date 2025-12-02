<?php

    $idf = request("id");
    if($obj = \App\Models\MediaItem::find($idf)) {
//        $obj->increment('count');
    } else {
        die("Media item not found");
    }

?>
@extends(getLayoutNameMultiReturnDefaultIfNull())

@section('title')

    Xem phim {{ $obj->name }} - {{ \App\Models\SiteMng::getTitle() }}

@endsection

@section('description')
    {{ $obj->description }} - {{ \App\Models\SiteMng::getDesc() }}
@endsection

@section('meta-keywords')<?php
                         echo \App\Models\SiteMng::getKeyword()
                         ?>
@endsection

@section('content')

    <!-- details -->
    <section class="section section--details">
        <!-- details content -->
        <div class="container">
            <div class="row">
                <!-- title -->
                <div class="col-12">
                    <h1 class="section__title section__title--head" data-code-pos='ppp17458463423601'>
                        {{$obj->name}}
                    </h1>
                </div>
                <!-- end title -->

                <!-- content -->
                <div class="col-12 col-xl-6">
                    <div class="item item--details">
                        <div class="row">
                            <!-- card cover -->
                            <div class="col-12 col-sm-5 col-md-5 col-lg-4 col-xl-6 col-xxl-5">
                                <div class="item__cover">
                                    <img src="{{ $obj->thumb }}" alt="">
                                    <span class="item__rate item__rate--green">8.4</span>
                                    <button class="item__favorite item__favorite--static" type="button"><i class="ti ti-bookmark"></i></button>
                                </div>
                            </div>
                            <!-- end card cover -->

                            <!-- card content -->
                            <div class="col-12 col-md-7 col-lg-8 col-xl-6 col-xxl-7">
                                <div class="item__content">
                                    <ul class="item__meta">
                                        <li><span>Đạo diễn:</span>
                                            <?php
                                            $mac = $obj->_authors;

                                            ?>
                                            @foreach($mac AS $ma1)

                                                <a href="{{$ma1->getLink1()}}">  {{$ma1->name }} </a>

                                            @endforeach


                                        </li>
                                        <li><span>Diễn viên:</span>

                                            <?php
                                            $mator = $obj->_actors;
                                            ?>
                                            @foreach($mator AS $ma1)
                                                <a href="{{$ma1->getLink1()}}">  {{$ma1->name }} </a>
                                            @endforeach

                                        </li>
                                        <li><span>Thể loại:</span>

                                            <?php
                                                $mfold = $obj->getCategory();

                                            ?>
                                            @foreach($mfold AS $fold)
                                                <a href="{{$fold->getLink1()}}">  {{$fold->name }} </a>
                                            @endforeach


                                        </li>
                                        <li><span>Năm:</span>
                                         {{ $obj->year }}
                                        </li>
                                        <li><span>Thời lượng:</span>
                                    {{ $obj->duration }}
                                        </li>
                                        <li><span>Quốc gia:</span> <a href="#">

                                                {{$obj->national}}

                                            </a></li>
                                    </ul>

                                    <div class="item__description">
                                        <p>
                                            {{$obj->description}}
                                         </p>

                                    </div>
                                </div>
                            </div>
                            <!-- end card content -->
                        </div>
                    </div>
                </div>
                <!-- end content -->

                <!-- player -->
                <div class="col-12 col-xl-6">
                    <video controls crossorigin playsinline poster="https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-HD.jpg" id="player">
                        <!-- Video files -->
                        <source src="https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-576p.mp4" type="video/mp4" size="576">
                        <source src="https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-720p.mp4" type="video/mp4" size="720">
                        <source src="https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-1080p.mp4" type="video/mp4" size="1080">

                        <!-- Caption files -->
                        <track kind="captions" label="English" srclang="en" src="https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-HD.en.vtt"
                               default>
                        <track kind="captions" label="Français" srclang="fr" src="https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-HD.fr.vtt">

                        <!-- Fallback for browsers that don't support the <video> element -->
                        <a href="https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-576p.mp4" download>Download</a>
                    </video>
                </div>
                <!-- end player -->
            </div>
        </div>
        <!-- end details content -->
    </section>
    <!-- end details -->

    <!-- content -->
    <section class="content">
        <div class="content__head content__head--mt">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <!-- content title -->
                        <h2 class="content__title">Discover</h2>
                        <!-- end content title -->

                        <!-- content tabs nav -->
                        <ul class="nav nav-tabs content__tabs" id="content__tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button id="1-tab" class="active" data-bs-toggle="tab" data-bs-target="#tab-1" type="button" role="tab" aria-controls="tab-1" aria-selected="true">Comments</button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button id="2-tab" data-bs-toggle="tab" data-bs-target="#tab-2" type="button" role="tab" aria-controls="tab-2" aria-selected="false">Reviews</button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button id="3-tab" data-bs-toggle="tab" data-bs-target="#tab-3" type="button" role="tab" aria-controls="tab-3" aria-selected="false">Photos</button>
                            </li>
                        </ul>
                        <!-- end content tabs nav -->
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-8">
                    <!-- content tabs -->
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab-1" role="tabpanel" aria-labelledby="1-tab" tabindex="0">
                            <div class="row">
                                <!-- comments -->
                                <div class="col-12">
                                    <div class="comments">
                                        <ul class="comments__list">
                                            <li class="comments__item">
                                                <div class="comments__autor">
                                                    <img class="comments__avatar" src="/_site/movie1/img/user.svg" alt="">
                                                    <span class="comments__name">John Doe</span>
                                                    <span class="comments__time">30.08.2018, 17:53</span>
                                                </div>
                                                <p class="comments__text">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text.</p>
                                                <div class="comments__actions">
                                                    <div class="comments__rate">
                                                        <button type="button"><i class="ti ti-thumb-up"></i>12</button>

                                                        <button type="button">7<i class="ti ti-thumb-down"></i></button>
                                                    </div>

                                                    <button type="button"><i class="ti ti-arrow-forward-up"></i>Reply</button>
                                                    <button type="button"><i class="ti ti-quote"></i>Quote</button>
                                                </div>
                                            </li>

                                            <li class="comments__item comments__item--answer">
                                                <div class="comments__autor">
                                                    <img class="comments__avatar" src="/_site/movie1/img/user.svg" alt="">
                                                    <span class="comments__name">John Doe</span>
                                                    <span class="comments__time">24.08.2018, 16:41</span>
                                                </div>
                                                <p class="comments__text">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
                                                <div class="comments__actions">
                                                    <div class="comments__rate">
                                                        <button type="button"><i class="ti ti-thumb-up"></i>8</button>

                                                        <button type="button">3<i class="ti ti-thumb-down"></i></button>
                                                    </div>

                                                    <button type="button"><i class="ti ti-arrow-forward-up"></i>Reply</button>
                                                    <button type="button"><i class="ti ti-quote"></i>Quote</button>
                                                </div>
                                            </li>

                                            <li class="comments__item comments__item--quote">
                                                <div class="comments__autor">
                                                    <img class="comments__avatar" src="/_site/movie1/img/user.svg" alt="">
                                                    <span class="comments__name">John Doe</span>
                                                    <span class="comments__time">11.08.2018, 11:11</span>
                                                </div>
                                                <p class="comments__text"><span>There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable.</span>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
                                                <div class="comments__actions">
                                                    <div class="comments__rate">
                                                        <button type="button"><i class="ti ti-thumb-up"></i>11</button>

                                                        <button type="button">1<i class="ti ti-thumb-down"></i></button>
                                                    </div>

                                                    <button type="button"><i class="ti ti-arrow-forward-up"></i>Reply</button>
                                                    <button type="button"><i class="ti ti-quote"></i>Quote</button>
                                                </div>
                                            </li>

                                            <li class="comments__item">
                                                <div class="comments__autor">
                                                    <img class="comments__avatar" src="/_site/movie1/img/user.svg" alt="">
                                                    <span class="comments__name">John Doe</span>
                                                    <span class="comments__time">07.08.2018, 14:33</span>
                                                </div>
                                                <p class="comments__text">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text.</p>
                                                <div class="comments__actions">
                                                    <div class="comments__rate">
                                                        <button type="button"><i class="ti ti-thumb-up"></i>99</button>

                                                        <button type="button">35<i class="ti ti-thumb-down"></i></button>
                                                    </div>

                                                    <button type="button"><i class="ti ti-arrow-forward-up"></i>Reply</button>
                                                    <button type="button"><i class="ti ti-quote"></i>Quote</button>
                                                </div>
                                            </li>

                                            <li class="comments__item">
                                                <div class="comments__autor">
                                                    <img class="comments__avatar" src="/_site/movie1/img/user.svg" alt="">
                                                    <span class="comments__name">John Doe</span>
                                                    <span class="comments__time">02.08.2018, 15:24</span>
                                                </div>
                                                <p class="comments__text">Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).</p>
                                                <div class="comments__actions">
                                                    <div class="comments__rate">
                                                        <button type="button"><i class="ti ti-thumb-up"></i>74</button>

                                                        <button type="button">13<i class="ti ti-thumb-down"></i></button>
                                                    </div>

                                                    <button type="button"><i class="ti ti-arrow-forward-up"></i>Reply</button>
                                                    <button type="button"><i class="ti ti-quote"></i>Quote</button>
                                                </div>
                                            </li>
                                        </ul>

                                        <!-- paginator mobile -->
                                        <div class="paginator-mob paginator-mob--comments">
                                            <span class="paginator-mob__pages">5 of 628</span>

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
                                        <ul class="paginator paginator--comments">
                                            <li class="paginator__item paginator__item--prev">
                                                <a href="#"><i class="ti ti-chevron-left"></i></a>
                                            </li>
                                            <li class="paginator__item"><a href="#">1</a></li>
                                            <li class="paginator__item paginator__item--active"><a href="#">2</a></li>
                                            <li class="paginator__item"><a href="#">3</a></li>
                                            <li class="paginator__item"><a href="#">4</a></li>
                                            <li class="paginator__item"><span>...</span></li>
                                            <li class="paginator__item"><a href="#">36</a></li>
                                            <li class="paginator__item paginator__item--next">
                                                <a href="#"><i class="ti ti-chevron-right"></i></a>
                                            </li>
                                        </ul>
                                        <!-- end paginator desktop -->

                                        <form action="#" class="sign__form sign__form--comments">
                                            <div class="sign__group">
                                                <textarea id="text" name="text" class="sign__textarea" placeholder="Add comment"></textarea>
                                            </div>

                                            <button type="button" class="sign__btn sign__btn--small">Send</button>
                                        </form>
                                    </div>
                                </div>
                                <!-- end comments -->
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab-2" role="tabpanel" aria-labelledby="2-tab" tabindex="0">
                            <div class="row">
                                <!-- reviews -->
                                <div class="col-12">
                                    <div class="reviews">
                                        <ul class="reviews__list">
                                            <li class="reviews__item">
                                                <div class="reviews__autor">
                                                    <img class="reviews__avatar" src="/_site/movie1/img/user.svg" alt="">
                                                    <span class="reviews__name">Best Marvel movie in my opinion</span>
                                                    <span class="reviews__time">24.08.2018, 17:53 by John Doe</span>

                                                    <span class="reviews__rating reviews__rating--yellow">6</span>
                                                </div>
                                                <p class="reviews__text">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text.</p>
                                            </li>

                                            <li class="reviews__item">
                                                <div class="reviews__autor">
                                                    <img class="reviews__avatar" src="/_site/movie1/img/user.svg" alt="">
                                                    <span class="reviews__name">Best Marvel movie in my opinion</span>
                                                    <span class="reviews__time">24.08.2018, 17:53 by John Doe</span>

                                                    <span class="reviews__rating reviews__rating--green">9</span>
                                                </div>
                                                <p class="reviews__text">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text.</p>
                                            </li>

                                            <li class="reviews__item">
                                                <div class="reviews__autor">
                                                    <img class="reviews__avatar" src="/_site/movie1/img/user.svg" alt="">
                                                    <span class="reviews__name">Best Marvel movie in my opinion</span>
                                                    <span class="reviews__time">24.08.2018, 17:53 by John Doe</span>

                                                    <span class="reviews__rating reviews__rating--red">5</span>
                                                </div>
                                                <p class="reviews__text">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text.</p>
                                            </li>
                                        </ul>

                                        <!-- paginator mobile -->
                                        <div class="paginator-mob paginator-mob--comments">
                                            <span class="paginator-mob__pages">5 of 628</span>

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
                                        <ul class="paginator paginator--comments">
                                            <li class="paginator__item paginator__item--prev">
                                                <a href="#"><i class="ti ti-chevron-left"></i></a>
                                            </li>
                                            <li class="paginator__item"><a href="#">1</a></li>
                                            <li class="paginator__item paginator__item--active"><a href="#">2</a></li>
                                            <li class="paginator__item"><a href="#">3</a></li>
                                            <li class="paginator__item"><a href="#">4</a></li>
                                            <li class="paginator__item"><span>...</span></li>
                                            <li class="paginator__item"><a href="#">36</a></li>
                                            <li class="paginator__item paginator__item--next">
                                                <a href="#"><i class="ti ti-chevron-right"></i></a>
                                            </li>
                                        </ul>
                                        <!-- end paginator desktop -->

                                        <form action="#" class="sign__form sign__form--comments">
                                            <div class="sign__group">
                                                <input type="text" class="sign__input" placeholder="Title">
                                            </div>

                                            <div class="sign__group">
                                                <select class="sign__select" name="rating" id="rating">
                                                    <option value="0">Rating</option>
                                                    <option value="1">1 star</option>
                                                    <option value="2">2 stars</option>
                                                    <option value="3">3 stars</option>
                                                    <option value="4">4 stars</option>
                                                    <option value="5">5 stars</option>
                                                    <option value="6">6 stars</option>
                                                    <option value="7">7 stars</option>
                                                    <option value="8">8 stars</option>
                                                    <option value="9">9 stars</option>
                                                    <option value="10">10 stars</option>
                                                </select>
                                            </div>

                                            <div class="sign__group">
                                                <textarea id="textreview" name="textreview" class="sign__textarea" placeholder="Add review"></textarea>
                                            </div>

                                            <button type="button" class="sign__btn sign__btn--small">Send</button>
                                        </form>
                                    </div>
                                </div>
                                <!-- end reviews -->
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab-3" role="tabpanel" aria-labelledby="3-tab" tabindex="0">
                            <!-- project gallery -->
                            <div class="gallery" itemscope>
                                <div class="row">
                                    <!-- gallery item -->
                                    <figure class="col-12 col-sm-6 col-xl-4" itemprop="associatedMedia" itemscope>
                                        <a href="/_site/movie1/img/gallery/project-1.jpg" itemprop="contentUrl" data-size="1920x1280">
                                            <img src="/_site/movie1/img/gallery/project-1.jpg" itemprop="thumbnail" alt="Image description" />
                                        </a>
                                        <figcaption itemprop="caption description">Some image caption 1</figcaption>
                                    </figure>
                                    <!-- end gallery item -->

                                    <!-- gallery item -->
                                    <figure class="col-12 col-sm-6 col-xl-4" itemprop="associatedMedia" itemscope>
                                        <a href="/_site/movie1/img/gallery/project-2.jpg" itemprop="contentUrl" data-size="1920x1280">
                                            <img src="/_site/movie1/img/gallery/project-2.jpg" itemprop="thumbnail" alt="Image description" />
                                        </a>
                                        <figcaption itemprop="caption description">Some image caption 2</figcaption>
                                    </figure>
                                    <!-- end gallery item -->

                                    <!-- gallery item -->
                                    <figure class="col-12 col-sm-6 col-xl-4" itemprop="associatedMedia" itemscope>
                                        <a href="/_site/movie1/img/gallery/project-3.jpg" itemprop="contentUrl" data-size="1920x1280">
                                            <img src="/_site/movie1/img/gallery/project-3.jpg" itemprop="thumbnail" alt="Image description" />
                                        </a>
                                        <figcaption itemprop="caption description">Some image caption 3</figcaption>
                                    </figure>
                                    <!-- end gallery item -->

                                    <!-- gallery item -->
                                    <figure class="col-12 col-sm-6 col-xl-4" itemprop="associatedMedia" itemscope>
                                        <a href="/_site/movie1/img/gallery/project-4.jpg" itemprop="contentUrl" data-size="1920x1280">
                                            <img src="/_site/movie1/img/gallery/project-4.jpg" itemprop="thumbnail" alt="Image description" />
                                        </a>
                                        <figcaption itemprop="caption description">Some image caption 4</figcaption>
                                    </figure>
                                    <!-- end gallery item -->

                                    <!-- gallery item -->
                                    <figure class="col-12 col-sm-6 col-xl-4" itemprop="associatedMedia" itemscope>
                                        <a href="/_site/movie1/img/gallery/project-5.jpg" itemprop="contentUrl" data-size="1920x1280">
                                            <img src="/_site/movie1/img/gallery/project-5.jpg" itemprop="thumbnail" alt="Image description" />
                                        </a>
                                        <figcaption itemprop="caption description">Some image caption 5</figcaption>
                                    </figure>
                                    <!-- end gallery item -->

                                    <!-- gallery item -->
                                    <figure class="col-12 col-sm-6 col-xl-4" itemprop="associatedMedia" itemscope>
                                        <a href="/_site/movie1/img/gallery/project-6.jpg" itemprop="contentUrl" data-size="1920x1280">
                                            <img src="/_site/movie1/img/gallery/project-6.jpg" itemprop="thumbnail" alt="Image description" />
                                        </a>
                                        <figcaption itemprop="caption description">Some image caption 6</figcaption>
                                    </figure>
                                    <!-- end gallery item -->
                                </div>
                            </div>
                            <!-- end project gallery -->
                        </div>
                    </div>
                    <!-- end content tabs -->
                </div>

                <!-- sidebar -->
                <div class="col-12 col-lg-4">
                    <div class="row">
                        <!-- section title -->
                        <div class="col-12">
                            <h2 class="section__title section__title--sidebar">Cùng thể loại</h2>
                        </div>
                        <!-- end section title -->

                        <?php
                            //Lấy ra cùng thể loại
                            $mfold = $obj->getCategory();
                            $mfold = $mfold->pluck('id')->toArray();
                            $items = \App\Models\MediaItem::whereHas('_folders', function ($query) use ($mfold) {
                                $query->whereIn('media_folder_id', $mfold);
                            })->paginate(6);
                        ?>

                        @foreach($items AS $one)
                        <!-- item -->
                        <div class="col-6 col-sm-4 col-lg-6">
                            <div class="item">
                                <div class="item__cover">
                                    <img src="{{$one->thumb}}" alt="">
                                    <a href="{{$one->getLink1()}}" class="item__play">
                                        <i class="ti ti-player-play-filled"></i>
                                    </a>
                                    <span class="item__rate item__rate--green"> {{ (80 + $one->id % 20) / 10  }}</span>
                                    </span>
                                    <button class="item__favorite" type="button"><i class="ti ti-bookmark"></i></button>
                                </div>
                                <div class="item__content">
                                    <h3 class="item__title"><a href="{{$one->getLink1()}}">
                                            {{$one->name}}
                                        </a></h3>
                                    <span class="item__category">
                                        <?php
                                        $mfold = $one->_folders->take(2);
                                        ?>
                                        @foreach($mfold AS $fold)
                                            <a href="{{$fold->getLink1()}}">  {{$fold->name }} </a>
                                        @endforeach

									</span>
                                </div>
                            </div>
                        </div>
                        <!-- end item -->
                        @endforeach
                    </div>
                </div>
                <!-- end sidebar -->
            </div>
        </div>
    </section>
    <!-- end content -->


@endsection
