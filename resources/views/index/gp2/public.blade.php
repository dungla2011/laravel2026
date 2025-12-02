@extends(getLayoutNameMultiReturnDefaultIfNull())

@section('logo')
    <?php echo \App\Models\SiteMng::getLogo() ?>
@endsection
@section('title')
    <?php
    echo \App\Models\SiteMng::getTitle()
    ?>
@endsection

@section('content')
    <section class="featured container clearfix">

        <?php
        $meta = new \App\Models\News_Meta();
        $mid = [];
        $obj = null;
        if (!$obj = \App\Models\News::where(['options' => 2, 'status' => 1])->orderByDesc('orders')->orderByDesc('created_at')->first()) {
            if ($obj = \App\Models\News::where(['status' => 1])->orderByDesc('orders')->orderByDesc('created_at')->first()) {
            }
        }
        if (!$obj) {
            bl("Không có tin tức nào được xuất bản?");
        } else {
            $mid[] = $obj->id;
        }

        ?>
        <?php
        if($obj){
        if ($obj instanceof \App\Models\News) ;
        ?>
        <article>
            <div class="thumb_big">

                <?php
                if ($idYoutube = \App\Components\Helper1::getIdYoutubeFromUrl($obj->image_list)) {
                    echo "<iframe width='100%' style='min-height: 270px' src='https://www.youtube.com/embed/$idYoutube' title='YouTube video player' frameborder='0' allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share' allowfullscreen></iframe>";
                } else {
                    $linkImg = $obj->image_list;
                    if (str_starts_with($linkImg, "http")) {

                    } else {
                        $linkImg = $obj->getThumbInImageList();
                    }
                    echo "<img style='width: 100%' src='$linkImg'>";
                }
                ?>
                {{--                <a class="thumb thumb_5x3" href="--}}
                {{--            /bi-mat-luat-hap-dan-chia-khoa-thanh-cong.2216.news">--}}
                {{--                    <img src="https://demo-tintuc.galaxycloud.vn/image_static/6756/5556/67565556575750/law.jpg ">--}}
                {{--                </a>--}}


            </div>
            <h1 class="title_news" data-code-pos="ppp1680782312632">
                <?php
                $link = $meta->getPublicLink($obj);
                ?>
                <a href="<?php
                echo $link;
                ?>">
                    <?php
                    echo $obj->name
                    ?>
                </a>
                <?php

                ?>
            </h1>

            <p class="description">
            <?php
            echo $obj->summary
            ?>
        </article>
        <?php
        }
        ?>

        <div class="sub_featured">
            <ul data-code-pos="ppp1680782361946" class="scrollbar-inner" id="list_sub_featured" style="">

                <?php
                //Lấy 10 tin top trang chủ
                $m1 = \App\Models\News::where(["options" => 1, 'status' => 1])->orderByDesc('orders')->limit(6)->get();
                $tt = count($m1);

                if ($tt < 10) {
                    foreach ($m1 AS $obj) {
                        $mid[] = $obj->id;
                    }
                    $m2 = \App\Models\News::where(['status' => 1])->orderByDesc('created_at')->limit(6)->get();
                    foreach ($m2 AS $o2) {
                        if (!in_array($o2->id, $mid)) {
                            $m1->push($o2);
                        }
                        if (count($m1) > 10)
                            break;
                    }
                }
                foreach ($m1 AS $obj){
                if ($obj instanceof \App\Models\News) ;
                $link = $meta->getPublicLink($obj);
                $mid[] = $obj->id;
                ?>


                <li data-code-pos="ppp1680782358418">
                    <a href="<?php echo $link ?>" title="<?php echo $obj->name ?>">
                        <?php
                        //                        echo $obj->id . " . ";
                        echo $obj->name
                        ?>
                    </a>
                </li>
                <?php
                }
                ?>

            </ul>
        </div>
        <div class="ads_featured">
            <section class="box_category qqqq1111" data-code-pos="ppp1680838513091">
                <?php
                $ui = \App\Models\BlockUi::showEditButtonStatic("qc-phai-trang-chu");
                $img = '';
                if($ui){
                    $img = $ui->getThumbInImageList();
                }
                ?>
                    <a target="_blank" href="<?php
                    echo strip_tags($ui->getSummary());
                    ?>">
                <img src="<?php
                echo $img
                ?>">
                    </a>
            </section>
        </div>
    </section>
    <!-- End featured -->


    <section class="container clearfix">
        <!-- Slidebar 1 -->
        <section class="sidebar_home_1 sidebar_flexible_1">

            <?php

            $mm = \App\Models\News::whereNotIn('id', $mid)->where(['status' => 1])->orderByDesc('created_at')->limit(6)->get();

            if(count($mm)){

            foreach ($mm as $item) {
            if ($item instanceof \App\Models\News) ;
            $link = $meta->getPublicLink($item);
            $img = $item->getThumbInImageList();
            $mid[] = $item->id;

            ?>
            <article class="art_item line-bottom">
                <h4 class="title_news"><a href="<?php
                    echo $link
                    ?>">
                        <?php
                        echo $item->name
                        ?>
                    </a>

                </h4>
                <div class="thumb_art">
                    <a class="thumb thumb_5x3" href="<?php
                    echo $link
                    ?>"><img src="<?php
                        echo $img
                        ?>"></a>

                </div>
                <p class="description">
                    <?php
                    echo $item->summary
                    ?>

                </p>

                <!--                <p class="related_news"><a href="#" title=""><i class="fa fa-square"></i>05 dấu ấn nổi bật của Tập đoàn-->
                <!--                        dầu khí quốc gia Việt Nam năm 2018</a></p>-->
            </article>

            <?php
            }
            }
            ?>

                <?php
                if(0){
                ?>

        <!-- BOX VIDEO -->
            <section class="box_category box_video clearfix">
                <div class="cat-head clearfix">
                    <h2><a href="http://Glx1.com.vn/tam-nhin-tv" title="GLX TV" class="cat-name">GLX TV</a></h2>
                </div>
                <figure class="wrap_video">
                    <div class="thumb thumb_video">
                        <iframe src="https://www.youtube.com/embed/au0UI6yunCA" frameborder="0"
                                allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen></iframe>
                    </div>
                </figure>
                <h4><a href="" data-code-pos="ppp1678769711060">David de Gea và Những pha bắt bóng thần thánh Thật khó
                        tin.</a></h4>
                <div class="slide_video clearfix">
                    <div class="owl-carousel">
                        <div class="art_item">
                            <div class="thumb_art">
                                <a class="thumb thumb_5x3" href="#" title="">
                                    <img src="/images/no-img.jpg">
                                    <span class="icon_thumb"><i class="fa fa-video-camera"></i></span>
                                </a>
                            </div>
                            <p class="title_news"><a href="#" title="">Nữ 9x kiếm tiền tỷ mỗi tháng từ vườn hồng<span
                                        class="icon_thumb"><i class="fa fa-video-camera"></i></span></a></p>
                        </div>
                        <div class="art_item">
                            <div class="thumb_art">
                                <a class="thumb thumb_5x3" href="#" title="">
                                    <img src="/images/no-img.jpg">
                                    <span class="icon_thumb"><i class="fa fa-video-camera"></i></span>
                                </a>
                            </div>
                            <p class="title_news"><a href="#" title="">Nữ 9x kiếm tiền tỷ mỗi tháng từ vườn hồng<span
                                        class="icon_thumb"><i class="fa fa-video-camera"></i></span></a></p>
                        </div>
                        <div class="art_item">
                            <div class="thumb_art">
                                <a class="thumb thumb_5x3" href="#" title="">
                                    <img src="/images/no-img.jpg">
                                    <span class="icon_thumb"><i class="fa fa-video-camera"></i></span>
                                </a>
                            </div>
                            <p class="title_news"><a href="#" title="">Nữ 9x kiếm tiền tỷ mỗi tháng từ vườn hồng<span
                                        class="icon_thumb"><i class="fa fa-video-camera"></i></span></a></p>
                        </div>
                        <div class="art_item">
                            <div class="thumb_art">
                                <a class="thumb thumb_5x3" href="#" title="">
                                    <img src="/images/no-img.jpg">
                                    <span class="icon_thumb"><i class="fa fa-video-camera"></i></span>
                                </a>
                            </div>
                            <p class="title_news"><a href="#" title="">Nữ 9x kiếm tiền tỷ mỗi tháng từ vườn hồng<span
                                        class="icon_thumb"><i class="fa fa-video-camera"></i></span></a></p>
                        </div>
                    </div>
                    <a class="btn_prev btn_video_prev" href="javascript:;"><i class="fa fa-angle-left"></i></a>
                    <a class="btn_next btn_video_next" href="javascript:;"><i class="fa fa-angle-right"></i></a>
                </div>
            </section>

                <?php
                }
                ?>

            <?php

            $mf = \App\Models\NewsFolder::where(['parent_id' => 0, 'status'=>1])->get();

            $cc = 0;
            $mFolderShowed = [];
            for($i = 0; $i < 6; $i++)
            {
            if(isset($mf[$i])){
            $fold = $mf[$i];
            $metaFolder = new \App\Models\NewsFolder_Meta();

            $mNews = \App\Models\News::where(['status' => 1, 'parent_id' => $fold->id])->whereNotIn('id', $mid)->orderByDesc('created_at')->limit(4)->get();
            if (!$mNews || count($mNews) == 0) {
//                    echo "<br/>\n Zero...";
                continue;
            }
            $cc++;
            $mFolderShowed[] = $fold->id;
            if ($cc > 2)
                break;
            ?>

            <section class="box_category  article_min clearfix">
                <div class="cat-head clearfix">
                    <h2><a href="<?php
                        echo $metaFolder->getPublicLink($fold);
                        ?>" class="cat-name">
                            <?php
                            echo $fold->name
                            ?>
                        </a></h2>
                    <div class="sub-cat">
{{--                        <a href="#" class="sub-cat-name">Góc luật sư</a><a--}}
{{--                            href="#" class="sub-cat-name">Góc đa chiều</a>--}}
                    </div>
                </div>


                <?php


                if($mNews[0]){
                $obj = $mNews[0];
                $link = $meta->getPublicLink($obj);
                if ($obj instanceof \App\Models\News) ;
                $img = $obj->getThumbInImageList();
                ?>
                <article class="art_item">
                    <h4 class="title_news"><a href="<?php
                        echo $link;
                        ?>">
                            <?php
                            echo $obj->name;
                            ?>
                        </a></h4>
                    <div class="thumb_art">
                        <a class="thumb thumb_5x3" href="#"><img
                                src="<?php
                                echo $img
                                ?>"></a>
                        <span class="icon_thumb"><i class="fa fa-video-camera"></i></span>
                    </div>
                    <p class="description">
                        <?php
                        echo $obj->summary
                        ?>
                    </p>
                    <?php
                    if($mNews[1]){
                    $obj = $mNews[1];
                    $link = $meta->getPublicLink($obj);
                    if ($obj instanceof \App\Models\News) ;
                    $img = $obj->getThumbInImageList();
                    ?>
                    <p class="related_news">
                        <a href="<?php
                        echo $link;
                        ?>" title=""><i
                                class="fa fa-square"></i>
                            <?php
                            echo $obj->name
                            ?>
                        </a></p>
                    <?php
                    }
                    ?>
                </article>
                <?php
                }
                ?>

                <ul class="list_title">
                    <?php
                    for($i1 = 2; $i1 <= 3; $i1++){
                    if(isset($mNews[$i1])){
                    $obj1 = $mNews[$i1];
                    $link = $meta->getPublicLink($obj1);
                    if ($obj1 instanceof \App\Models\News) ;
                    $img = $obj1->getThumbInImageList();
                    ?>
                    <li>
                        <h4><a href="<?php echo $link ?>" title="">
                                <?php
                                echo $obj1->name
                                ?>
                            </a>
                        </h4>
                    </li>
                    <?php
                    }
                    }
                    ?>
                </ul>
            </section>

        <?php
        }
        }
        ?>


        <!-- BOX TIN ẢNH -->
            <section class="box_category box_tinanh clearfix" style="display: none">
                <div class="cat-head clearfix">
                    <h2><a href="http://Glx1.com.vn/tam-nhin-tv" title="GLX TV" class="cat-name">Tin ảnh</a></h2>
                </div>
                <article class="art_item">
                    <div class="thumb_art thumb_big">
                        <a class="thumb thumb_5x3" href="#"><img src=""></a>
                        <span class="icon_thumb"><i class="fa fa-camera"></i></span>
                    </div>
                    <h4 class="title_news">
                        <a href="#">TP HCM đồng loạt 'đòi vỉa hè' cho người đi bộ TP HCM đồng loạt 'đòi vỉa hè' cho
                            người đi bộ<span class="icon_thumb"><i class="fa fa-camera"></i></span></a>
                    </h4>
                </article>
                <div class="slide_video clearfix">
                    <div class="owl-carousel">
                        <div class="art_item ">
                            <div class="thumb_art">
                                <a class="thumb thumb_5x3" href="#" title="">
                                    <img src="/images/no-img.jpg">
                                    <span class="icon_thumb"><i class="fa fa-camera"></i></span>
                                </a>
                            </div>
                            <p class="title_news"><a href="#" title="">Nữ 9x kiếm tiền tỷ mỗi tháng từ vườn hồng<span
                                        class="icon_thumb"><i class="fa fa-camera"></i></span></a></p>
                        </div>
                        <div class="art_item">
                            <div class="thumb_art">
                                <a class="thumb thumb_5x3" href="#" title="">
                                    <img src="/images/no-img.jpg">
                                    <span class="icon_thumb"><i class="fa fa-camera"></i></span>
                                </a>
                            </div>
                            <p class="title_news"><a href="#" title="">Nữ 9x kiếm tiền tỷ mỗi tháng từ vườn hồng<span
                                        class="icon_thumb"><i class="fa fa-camera"></i></span></a></p>
                        </div>
                        <div class="art_item">
                            <div class="thumb_art">
                                <a class="thumb thumb_5x3" href="#" title="">
                                    <img src="">
                                    <span class="icon_thumb"><i class="fa fa-camera"></i></span>
                                </a>
                            </div>
                            <p class="title_news"><a href="#" title="">Nữ 9x kiếm tiền tỷ mỗi tháng từ vườn hồng<span
                                        class="icon_thumb"><i class="fa fa-camera"></i></span></a></p>
                        </div>
                        <div class="art_item">
                            <div class="thumb_art">
                                <a class="thumb thumb_5x3" href="#" title="">
                                    <img src="">
                                    <span class="icon_thumb"><i class="fa fa-camera"></i></span>
                                </a>
                            </div>
                            <p class="title_news"><a href="#" title="">Nữ 9x kiếm tiền tỷ mỗi tháng từ vườn hồng<span
                                        class="icon_thumb"><i class="fa fa-camera"></i></span></a></p>
                        </div>
                    </div>
                    <a class="btn_prev btn_video_prev" href="javascript:;"><i class="fa fa-angle-left"></i></a>
                    <a class="btn_next btn_video_next" href="javascript:;"><i class="fa fa-angle-right"></i></a>
                </div>
            </section>

        </section>
        <!-- End Slidebar 1 -->


        <!-- Slidebar 2 -->
        <section class="sidebar_home_2">

            <?php
            $mf2 = \App\Models\NewsFolder::where(['parent_id' => 0, 'status'=>1])->whereNotIn('id', $mFolderShowed)->orderByDesc('created_at')->limit(10)->get();



            if(($mf2)){
            $cc1 = 0;
            foreach ($mf2 AS $fold){
            $cc1++;
            ?>
            <section data-code-pos="ppp1680794068659" class="box_category  article_min clearfix <?php

            if ($cc1 > 1)
                echo 'line-top'

            ?> ">
                <div class="cat-head clearfix">
                    <h2><a href="<?php
                        echo $metaFolder->getPublicLink($fold);
                        ?>" class="cat-name">
                            <?php
                            echo $fold->name;
                            ?>

                        </a></h2>
                    <div class="sub-cat" data-code-pos="ppp1680794084924">
                        <a href="#" class="sub-cat-name">Góc luật sư</a>
                        <a
                            href="#" class="sub-cat-name">Góc đa chiều</a>
                    </div>
                </div>

                <?php
                $mNews = \App\Models\News::where(['status' => 1, 'parent_id' => $fold->id])->whereNotIn('id', $mid)->orderByDesc('created_at')->limit(4)->get();
                if(!$mNews || !count($mNews)){
                    echo "<br/>\n Empty news";
                }else{
                //                    echo "<br/>\n Zero...";
                if(isset($mNews[0])){
                $obj1 = $mNews[0];
                $link = $meta->getPublicLink($obj1);
                if ($obj1 instanceof \App\Models\News) ;
                $img = $obj1->getThumbInImageList();
                $mid[] = $obj1->id;

                ?>
                <article class="art_item">
                    <h4 class="title_news"><a href="<?php echo $link ?>">
                            <?php
                            echo $obj1->name;
                            ?>
                        </a></h4>
                    <div class="thumb_art">
                        <a class="thumb thumb_5x3" href="#"><img
                                src="<?php
                                echo $img
                                ?>"></a>
                        <span class="icon_thumb"><i class="fa fa-video-camera"></i></span>
                    </div>
                    <p class="description">
                        <?php
                        echo $obj1->summary
                        ?>
                    </p>

                    <?php
                    if(isset($mNews[1])){
                    $obj1 = $mNews[1];
                    $link = $meta->getPublicLink($obj1);
                    if ($obj1 instanceof \App\Models\News) ;
                    $img = $obj1->getThumbInImageList();
                    $mid[] = $obj1->id;
                    ?>
                    <p class="related_news">
                        <a href="<?php
                        echo $link
                        ?>" title=""><i class="fa fa-square"></i>
                            <?php
                            echo $obj1->name;
                            ?>
                        </a>
                    </p>
                    <?php
                    }
                    ?>
                </article>

                <?php
                }
                ?>
                <ul class="list_title">
                    <?php
                    for($i3 = 2; $i3 < 4; $i3++){
                        if (isset($mNews[$i3])) {
                            $obj1 = $mNews[$i3];
                            $link = $meta->getPublicLink($obj1);
                            if ($obj1 instanceof \App\Models\News) ;
                            $img = $obj1->getThumbInImageList();
                            $mid[] = $obj1->id;

                        ?>
                        <li>
                            <h4><a href="<?php
                            echo $link
                            ?>" title="">
                                    <?php
                                    echo $obj1->name
                                    ?>
                                </a>
                            </h4>
                        </li>
                        <?php
                        }
                    }
                    ?>

                </ul>

                <?php
                }
                ?>
            </section>

        <?php
        }
        }
        ?>








        <!-- TIN MỚI NHÂT & TIN ĐỌC NHIỀU -->
            <section class="box_category twin-box clearfix">
                <div class="twin-col">
                    <h2><a href="">Tin mới nhất</a></h2>
                    <ul>
                        <?php
                        $m1 = \App\Models\News::where(['status' => 1])->orderByDesc('created_at')->limit(5)->get();

                        foreach ($m1 AS $obj){
                        if ($obj instanceof \App\Models\News) ;
                        $link = $meta->getPublicLink($obj);
                        $img = $obj->getThumbInImageList();
                        ?>
                        <li>
                            <h4><a href="<?php
                            echo $link
                            ?>" title="this">
                                    <?php
                                    echo $obj->name
                                    ?>
                                </a></h4>
                        </li>
                        <?php
                        }
                        ?>
                    </ul>
                </div>

                <div class="twin-col">
                    <h2><a href="">Tin đọc nhiều</a></h2>

                        <ul>
                            <?php
                            $m1 = \App\Models\News::where(['status' => 1])->orderByDesc('count_view')->limit(5)->get();

                            foreach ($m1 AS $obj){
                            if ($obj instanceof \App\Models\News) ;
                            $link = $meta->getPublicLink($obj);
                            $img = $obj->getThumbInImageList();
                            ?>
                            <li>
                                <h4><a href="<?php
                                    echo $link
                                    ?>" title="this">
                                        <?php
                                        echo $obj->name
                                        ?>
                                    </a></h4>
                            </li>
                            <?php
                            }
                            ?>
                        </ul>

                </div>
            </section>

{{--            <section class="box_category clearfix">--}}
{{--                <div class="width_common banner_ads hide-mobile">--}}
{{--                    <img src="https://via.placeholder.com/300x200">--}}
{{--                </div>--}}
{{--            </section>--}}

        </section>
        <!-- End Slidebar 2 -->
        <!-- Slidebar 3 -->
        <section class="sidebar_home_3">
            <section class="box_category">
                <div class="width_common banner_ads" data-code-pos='ppp17248388742951'>
                    <img src="https://via.placeholder.com/160x300">
                </div>
            </section>
            <section class="box_category">
                <div class="width_common banner_ads">
                    <img src="https://via.placeholder.com/160x300">
                </div>
            </section>
        </section>
        <!-- End Slidebar 3 -->

    </section>
    <!-- END MANIN -->
    <section class="container clearfix" style="display: none">
        <div class="width_common banner_ads banner_ads_bottom">
            <img src="https://via.placeholder.com/728x90">
        </div>
    </section>

    <section class="container thongtin_dn" style="display: none">
        <header>
            <h2>Thông tin doanh nghiệp</h2>
        </header>
        <ul class="wrap_list owl-carousel">
            <li class="list_dn">
                <a href="#" title=""><img src="/images/no-img.jpg">Cà phê hoà tan nổi tiếng châu Âu có mặt tại thị
                    trường Việt Nam</a>
            </li>
            <li class="list_dn">
                <a href="#" title=""><img src="/images/no-img.jpg">Cà phê hoà tan nổi tiếng châu Âu có mặt tại thị
                    trường Việt Nam</a>
            </li>
            <li class="list_dn">
                <a href="#" title=""><img src="/images/no-img.jpg">Cà phê hoà tan nổi tiếng châu Âu có mặt tại thị
                    trường Việt Nam</a>
            </li>
            <li class="list_dn">
                <a href="#" title=""><img src="/images/no-img.jpg">Cà phê hoà tan nổi tiếng châu Âu có mặt tại thị
                    trường Việt Nam</a>
            </li>
        </ul>
    </section>



@endsection
