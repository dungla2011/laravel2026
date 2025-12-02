@extends(getLayoutNameMultiReturnDefaultIfNull())

@section("fav_icon")<?php
echo \App\Models\SiteMng::getLogoIcon()
?>@endsection


@section("og_desc")<?php
echo \App\Models\SiteMng::getDesc()
?>@endsection

@section("og_image")<?php
echo \App\Models\SiteMng::getLogo()
?>@endsection

@section("title")<?php
echo \App\Models\SiteMng::getTitle()
?>@endsection


@section('content')

    <style>

        .carousel-caption {
            text-shadow: 2px 2px #222222;
        }

        .splide__slide img {
            width: 100%;
            height: auto;
        }
        .splide__list li{
            border: 1px solid #ccc
        }
    </style>

    <?php
    use App\Models\BlockUi;
    ?>



    <!-- Carousel -->
    <div id="demo" class="qqqq1111 carousel slide container-lg" data-bs-ride="carousel"
         data-code-pos='ppp16897794749041'>
    <?php
    $ui = BlockUi::showEditButtonStatic('slide-img-home-page');
    $mImg = $ui->getAllImageList();

    ?>

    <!-- Indicators/dots -->
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#demo" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#demo" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#demo" data-bs-slide-to="2"></button>
        </div>


        <!-- The slideshow/carousel -->
        <div class="carousel-inner" data-code-pos='ppp16897794686451'>

            <?php

            $sum = $ui->getSummary();
            $sum = str_replace("<br/>", "\n", $sum);
            $sum = str_replace("<br>", "\n", $sum);
            $sum = str_replace("<br />", "\n", $sum);
            $sum = trim($sum);

            $mLine = explode("\n", $sum);

            $cc = 0;
            $act = '';
            foreach ($mImg AS $img) {
                if ($cc == 0)
                    $act = 'active';
                echo "<div class='carousel-item $act'>
                    <img src='$img' alt='' class='d-block' style='width:100%'>
                    ";


                $line1 = $mLine[$cc] ?? '';
                $line2 = $mLine[$cc + 1] ?? '';

                if ($line1 && $line2)
                    echo "<div class='carousel-caption'><h3>$line1</h3><p>$line2</p></div>";

                echo "\n</div>";
                $cc++;
            }
            ?>
        </div>

        <!-- Left and right controls/icons -->
        <button class="carousel-control-prev" type="button" data-bs-target="#demo" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#demo" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>


    <?php
    $mmFold = \App\Models\ProductFolder::where(['front' => 1])->get();
    if($mmFold->count()){
    ?>

    <div class="container pt-2" data-code-pos='ppp16897794803421'>
        <div class="header_name">

                <span class="title">
                    <i class="fas fa-table"></i>
                    <?php
                    //                    $folder = \App\Models\ProductFolder::find(1);
                    $folder = $mmFold[0];

                    if ($folder instanceof \App\Models\ProductFolder) ;
                    ?>
                    <a href="<?php echo $folder ? $folder->getLinkPublic() : ''; ?>">
                        <?php
                        echo $folder ? $folder->name : '';
                        ?>
                    </a>
                </span>
            <a class="view_more" href="<?php echo $folder ? $folder->getLinkPublic() : ''; ?>">
                Xem thêm <i class="fa fa-caret-right"></i>
            </a>
        </div>
        <div id="thumbnail-slider" class="splide">
            <div class="splide__track">
                <ul class="splide__list">
                    <?php

                    $mm = \App\Models\Product::where("parent_id", $folder->id)->orderByDesc('created_at')->where("status", 1)->limit(12)->get();
                    //for($i =0; $i<10; $i++)
                    foreach ($mm AS $obj)
                    {
                    if ($obj instanceof \App\Models\Product) ;
                    $img = $obj->getThumbInImageListWithNoImg();
                    $link = $obj->getLinkPublic();

                    ?>

                    <li title="001" class="splide__slide">

                        <img style="" src="<?php echo $img?>" alt="">

                        <div class="product_title" style="">

                            <a style="text-decoration: none; font-size: 0.9em" href="<?php echo $link?>">
                                <?php

                                echo $obj->name;
                                ?>
                            </a>


                            <span style="font-size: small; color: brown; display: block">

                        Giá:
                        <?php

                                if($obj->price){
                                    echo "\n ".number_formatvn0($obj->price)." VNĐ";

                                    if($obj->price1){
                                        echo " <span style='text-decoration: line-through; display: inline-block'> ". number_formatvn0($obj->price1)." </span>";
                                    }
                                }
                                else{
                                    echo "\n Liên hệ";
                                }

                                ?>

                    </span>

                        </div>

                    </li>
                    <?php
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>

    <?php
    for($i = 1; $i < $mmFold->count(); $i++)
    {
    ?>
    <div class="container pt-2" data-code-pos='ppp16897759651'>
        <div class="header_name">
                <span class="title">
                    <i class="fas fa-table"></i>
                    <?php
                    //                    $folder = \App\Models\ProductFolder::find(3);

                    $folder = $mmFold[$i];



                    if ($folder instanceof \App\Models\ProductFolder) ;
                    ?>
                    <a href="<?php echo $folder ? $folder->getLinkPublic() : ''; ?>">
                        <?php
                        echo $folder ? $folder->name : '';
                        ?>
                    </a>
                </span>
            <a class="view_more" href="<?php echo $folder ? $folder->getLinkPublic() : ''; ?>">
                Xem thêm
            </a>
        </div>

        <div class="row product_row1">
            <?php
            $mm = \App\Models\Product::where("parent_id", $folder->id)->where("status", 1)->orderByDesc('created_at')->limit(12)->get();
            //for($i =0; $i<10; $i++)
            foreach ($mm AS $obj)
            {
            if ($obj instanceof \App\Models\Product) ;
            $img = $obj->getThumbInImageListWithNoImg();
            $link = $obj->getLinkPublic();
            ?>
            <div data-code-pos='ppp17060688983431' class="col-6 col-md-4 col-lg-3 col-xl-2 one-product txt-center">
                <a href="<?php echo $link  ?>">
                    <div>
                        <button class="btn d-none"> +</button>
                        <img src="<?php echo $img ?>" class="img-fluid">
                    </div>
                    <span class="" data-code-pos='ppp17060688952701'>
                    <?php
                        echo $obj->name
                        ?>
                    </span>
                    <span style="font-size: small; color: brown">
                        Giá:
                        <?php

                        if($obj->price){
                            echo "\n ".number_formatvn0($obj->price)." VNĐ";

                            if($obj->price1){
                                echo " <span style='text-decoration: line-through; display: inline-block'> ". number_formatvn0($obj->price1)." </span>";
                            }
                        }
                        else{
                            echo "\n Liên hệ";
                        }

                        ?>

                    </span>
                </a>
            </div>
            <?php
            }
            ?>
        </div>
    </div>

    <?php
    }
    ?>



    <?php
    }
    ?>

    <div class="container pt-2" data-code-pos='ppp16897794949451'>

        <div class="header_name">
                <span class="title qqqq1111">
                    <i class="fas fa-table"></i>
                    <?php
                    if($ui = BlockUi::showEditButtonStatic('title-block-news'))
                        echo $ui->getName();
                    ?>
                </span>
            <a  class="view_more" href="/tin-tuc">
                Xem thêm
            </a>
        </div>

        <div class="row product_new1">

            <?php

            $mm = \App\Models\News::where('status', 1)->limit(8)->get();

            foreach($mm AS $news){
            if($news instanceof \App\Models\News);
            ?>
            <div class="col-6 col-md-6 col-lg-4 col-xl-3">
                <div>
                    <a href="{{$news->getLinkPublic()}}">
                        <img src="{{$news->getThumbInImageListWithNoImg()}}" class="img-fluid"> <span> {{$news->name}}</span>
                    </a>
                </div>
            </div>
            <?php
            }
            ?>

        </div>



    </div>

    <div class="container pt-4 mt-3 bg-light qqqq1111" data-code-pos='ppp16897794976591'>

        <?php
        $ui = BlockUi::showEditButtonStatic("banner-bottom");
        ?>

        <div class="row">

            <div class="col-sm-4  mb-2">
                <img src="<?php
                if ($ui)
                    echo $ui->getThumbInImageListWithNoImg();
                ?>" class="w-100" alt="">
            </div>

            <div class="col-sm-8 " data-code-pos='ppp16901004047381'>

                <div class="header_name mt-0">
                        <span class="title">
                            <i class="fa fa-cogs"></i>
                             <?php
                            if ($ui)
                                echo $ui->getName();
                            ?>
                        </span>
                </div>

                <?php
                if ($ui)
                    echo $ui->getContent();
                ?>


            </div>


        </div>


    </div>

    <div class="container pt-2 qqqq1111" data-code-pos='ppp16897795008271'>
        <?php

        $ui = BlockUi::showEditButtonStatic('doi-tac');

        ?>

        <div class="product_partner">
                <span class="title_center">
                    <i class="fas fa-handshake"></i>
                    <?php
                    if ($ui) {
                        echo $ui->name;
                        $mImg = $ui->getAllImageList();

                    } else
                        echo "Đối tác của chúng tôi";
                    ?>
                </span>

        </div>
        <div id="thumbnail-slider2" class="splide partner">
            <div class="splide__track">
                <ul class="splide__list">

                    <?php
                    if($ui && isset($mImg)){
                    if($mImg && is_array($mImg))
                    foreach ($mImg AS $img){
                    ?>
                    <li title="" class="splide__slide">
                        <img data-splide-lazy="<?php echo $img ?>" alt="">
                    </li>
                    <?php
                    }
                    }
                    ?>

                    {{--                        <li class="splide__slide">--}}
                    {{--                            <img src="/template/d-hoa/images/01.jpg" alt="">--}}
                    {{--                        </li>--}}
                    {{--                        <li class="splide__slide">--}}
                    {{--                            <img src="/template/d-hoa/images/02.jpg" alt="">--}}
                    {{--                        </li>--}}
                    {{--                        <li class="splide__slide">--}}
                    {{--                            <img src="/template/d-hoa/images/03.jpg" alt="">--}}
                    {{--                        </li>--}}
                    {{--                        <li class="splide__slide">--}}
                    {{--                            <img src="/template/d-hoa/images/04.jpg" alt="">--}}
                    {{--                        </li>--}}
                    {{--                        <li class="splide__slide">--}}
                    {{--                            <img src="/template/d-hoa/images/05.jpg" alt="">--}}
                    {{--                        </li>--}}
                    {{--                        <li class="splide__slide">--}}
                    {{--                            <img src="/template/d-hoa/images/06.jpg" alt="">--}}
                    {{--                        </li>--}}
                    {{--                        <li class="splide__slide">--}}
                    {{--                            <img src="/template/d-hoa/images/07.jpg" alt="">--}}
                    {{--                        </li>--}}
                    {{--                        <li class="splide__slide">--}}
                    {{--                            <img src="/template/d-hoa/images/08.jpg" alt="">--}}
                    {{--                        </li>--}}


                </ul>
            </div>
        </div>
    </div>




@endsection
