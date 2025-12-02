@extends(getLayoutNameMultiReturnDefaultIfNull())



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

        .container {

        }

        .carousel-caption {
            text-shadow: 2px 2px #222222;
        }

        .splide__slide img {
            width: 100%;
            height: auto;
        }

        @media (max-width: 768px) {
            /* Khi màn hình có chiều rộng tối đa 768px, chuyển cột 2 lên trên cột 1 */
            .flex1 {
                flex-direction: column-reverse;
            }
        }
        .header_topic{
            background-color: orangered; position: relative;
            border-radius: 15px 15px 0px 0px;
        }


    </style>

    <?php
    use App\Models\BlockUi;
    ?>



    <!-- Carousel -->
    <div id="demo" class="qqqq1111 carousel slide container-fluid" style="background-color: red; --bs-gutter-x: 0px"
         data-bs-ride="carousel"
         data-code-pos='ppp1684749041'>
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
                Xem thêm
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
    <div class="container pt-2" data-code-pos='ppp16897794859651'>
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
            <div class="col-6 col-md-4 col-lg-3 col-xl-2 one-product txt-center">
                <a href="<?php echo $link  ?>">
                    <div>
                        <button class="btn d-none"> +</button>
                        <img src="<?php echo $img ?>" class="img-fluid">
                    </div>
                    <span class="">
                    <?php
                        echo $obj->name
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

    $ui = BlockUi::showEditButtonStatic('title-block-news');

    ?>

    <div class="container pt-2" data-code-pos='ppp16897794949451'>

        <?php
        if($ui && $ui->status){
        ?>

        <div class="header_name">
                <span class="title qqqq1111">
                    <i class="fas fa-table"></i>
                    <?php
                    if ($ui)
                        echo $ui->getName();
                    ?>
                </span>
            <a class="view_more" href="/tin-tuc">
                Xem thêm
            </a>
        </div>
        <?php
        }
        ?>

        <div class="row product_new1">

            <?php

            $mm = \App\Models\News::where('status', 1)->limit(8)->get();

            foreach($mm AS $news){
            if ($news instanceof \App\Models\News) ;
            ?>
            <div class="col-6 col-md-6 col-lg-4 col-xl-3">
                <div>
                    <a href="{{$news->getLinkPublic()}}">
                        <img src="{{$news->getThumbInImageListWithNoImg()}}" class="img-fluid">
                        <span> {{$news->name}}</span>
                    </a>
                </div>
            </div>
            <?php
            }
            ?>

        </div>
    </div>


    <div class="container pt-4 mt-3  qqqq1111" data-code-pos='ppp168667949'>

        <h3 class="txt-center">Bước đầu khám phá thế giới lập trình cùng Scratch
        </h3>

        <div class="mx-auto mt-3 mb-4" style="max-width: 1000px; ">

            <p>
            Lập trình được coi là ngôn ngữ toàn cầu giúp em tự tin hội nhập thời đại số 4.0. Hiểu được điều đó, bố mẹ
            lựa chọn cho em học lập trình từ sớm (tiểu học) để xây dựng nền tảng kiến thức công nghệ.

                <p>
                <b>
            Lập trình Scratch
                </b>
            </p>
                    <ul>
                        <li>
                            Scratch là  gì: là ngôn ngữ cao cấp khởi đầu tuyệt vời cho học lập trình.

                            Học viện MIT (Massachuset USA) là học viện đứng đầu về Công nghệ của Mỹ đã tạo ra ngôn ngữ lập trình này.
                        </li>
                        <li>
                            Đối tượng học Scratch: trẻ em từ 5 tuổi đã có thể học. Scratch phát triển theo hướng
                            tương tác trực quan, đồ họa sống động, giúp em nhanh chóng tiếp thu ngay từ những bài học đầu tiên
                        </li>
                        <li>
                            Scratch có thể làm gì: Scratch có thể làm rất nhiều việc, như Vẽ hình đồ họa, Làm toán, tạo Game ... rất nhanh chóng.
                            Ngoài ra Scratch cũng ứng dụng trong các khóa học về Robotics, tự động hóa - Một xu hướng lớn hiện nay.
                        </li>
                    </ul>
                </li>


            <p>
            <b>

                    Lập trình Python
            </b>
            </p>
                <ul>
                    <li>
                        Là ngôn ngữ Lập trình phổ biến hàng đầu, ứng dụng trong nhiều lĩnh vực, từ Lập trình Web, App, Cơ Sở dữ liệu...và đặc biệt là Trí Tuệ Nhân tạo (AI)
                    </li>
                    <li>
                        Ai có thể học Python: các bạn học sinh đã học Scratch, hoặc các học sinh cấp 2 trở lên là hoàn toàn có thể học tốt Python
                    </li>
                    <li>
                        Học Python để làm gì: Với công việc, thì Python là lựa chọn hàng đầu trong ngành IT.
                        Với học tập, thì Python luyện tư duy Logic rất tốt, và hỗ trợ cho việc thi tốt nghiệp phổ thông trung học, cũng như là điểm chọn xét tuyển vào trong các trường công nghệ hàng đầu.
                    </li>
                </ul>

            </ul>
            </p>
        </div>
        <img src="/slink/30/000/002/390b080100" style="width: 100%" alt="">
    </div>





    <div class="container pt-2 mt-3   flex1" data-code-pos='ppp16857567667949'
         style="display: flex; flex-wrap: wrap;">

        <div style=" flex: 1; /* Cột 1 co giãn theo tỷ lệ bằng nhau */
  /*background-color: #f2f2f2;*/
  padding: 20px; ">
            <h3>

                Khoá học lập trình Scratch sẽ giúp em:

            </h3>

            <p></p>

            <ul>
                <li>

                    Làm quen kiến thức lập trình: Khoá học Scratch giúp em bước đầu tiếp xúc và hiểu được "điều kì
                    diệu" xảy ra
                    bên trong phần mềm máy tính. Em sẽ được dạy cách lập trình qua thao tác kéo thả đơn giản để tăng sự
                    hào
                    hứng và kích thích trí tò mò trước khi em chuyển sang các câu lệnh phức tạp hơn.
                </li>
                <li>
                    Rèn luyện tư duy, kích thích sáng tạo: Tại các lớp học, em học cách tư duy logic thông qua
                    việc
                    lên ý tưởng, xây dựng cốt truyện, sắp xếp, di chuyển các câu lệnh sao cho tối ưu nhất. Bên cạnh đó
                    em cũng
                    là người sáng tạo về màu sắc, bố cục, giao diện,… để tạo ra một sản phẩm hoàn chỉnh từ hình thức đến
                    kỹ
                    thuật.

                </li>
                <li>
                    Trau dồi kỹ năng mềm: Em được trau dồi các kỹ năng mềm như thiết kế, thuyết trình, làm việc nhóm.
                    Môi
                    trường khuyến khích học sinh trở nên năng động, chủ động và tự giác và bảo vệ quan điểm, ý kiến của
                    mình.

                </li>
                <li>

                    Nuôi dưỡng đam mê công nghệ: Kết thúc mỗi khoá học, em có trong tay ít nhất 3 sản phẩm công nghệ
                    hoàn chỉnh
                    như: lập trình trò chơi, thiệp mời điện tử, câu chuyện về các nhân vật,… Đây chính là động lực để
                    em tiếp
                    tục chinh phục công nghệ trong tương lai.
                    <a id="lap-trinh-scratch"></a>
                </li>
            </ul>
        </div>
        <div style=" flex: 1; /* Cột 2 co giãn theo tỷ lệ bằng nhau */
  /*background-color: #ccc;*/
  padding: 20px;">


            <img style="width: 100%" class="pt-3" src="/slink/30/000/002/3002010800" alt="">


        </div>
    </div>


    <div class="container pt-2 mt-3  flex1" data-code-pos='ppp16857567667949'
         style="">
        <div class="pt-2 pb-1 text-center text-white header_topic" style="">

            <div style="position: absolute; top: -100px; color: transparent"><a id="sbasic" style="color: red">  </a> </div>
            <h2>Lập trình Scratch Cơ bản</h2>

        </div>

        <div class="container-fluid pt-2 pb-4 bg-light qqqq1111" data-code-pos='ppp16866667949'>
            <div class="container">
                <div class="row">
                    <div class="col-6 col-lg-3 text-center px-1 pt-4">
                        <svg width="32" height="32" viewBox="0 0 30 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M28.535 25.8281C27.7981 24.0827 26.7288 22.4973 25.3866 21.1602C24.0484 19.8192 22.4633 18.75 20.7186 18.0117C20.703 18.0039 20.6874 18 20.6717 17.9922C23.1053 16.2344 24.6874 13.3711 24.6874 10.1406C24.6874 4.78906 20.3514 0.453125 14.9999 0.453125C9.64829 0.453125 5.31235 4.78906 5.31235 10.1406C5.31235 13.3711 6.89438 16.2344 9.32798 17.9961C9.31235 18.0039 9.29673 18.0078 9.2811 18.0156C7.5311 18.7539 5.96079 19.8125 4.61314 21.1641C3.27216 22.5022 2.203 24.0874 1.4647 25.832C0.739393 27.5401 0.34822 29.3713 0.312354 31.2266C0.311311 31.2683 0.318624 31.3097 0.333861 31.3486C0.349098 31.3874 0.371951 31.4228 0.401074 31.4526C0.430197 31.4825 0.465 31.5062 0.503432 31.5224C0.541865 31.5386 0.58315 31.5469 0.624854 31.5469H2.9686C3.14048 31.5469 3.2772 31.4102 3.2811 31.2422C3.35923 28.2266 4.57017 25.4023 6.71079 23.2617C8.92563 21.0469 11.867 19.8281 14.9999 19.8281C18.1327 19.8281 21.0741 21.0469 23.2889 23.2617C25.4295 25.4023 26.6405 28.2266 26.7186 31.2422C26.7225 31.4141 26.8592 31.5469 27.0311 31.5469H29.3749C29.4166 31.5469 29.4578 31.5386 29.4963 31.5224C29.5347 31.5062 29.5695 31.4825 29.5986 31.4526C29.6278 31.4228 29.6506 31.3874 29.6658 31.3486C29.6811 31.3097 29.6884 31.2683 29.6874 31.2266C29.6483 29.3594 29.2616 27.543 28.535 25.8281ZM14.9999 16.8594C13.2069 16.8594 11.5194 16.1602 10.2499 14.8906C8.98032 13.6211 8.2811 11.9336 8.2811 10.1406C8.2811 8.34766 8.98032 6.66016 10.2499 5.39062C11.5194 4.12109 13.2069 3.42188 14.9999 3.42188C16.7928 3.42188 18.4803 4.12109 19.7499 5.39062C21.0194 6.66016 21.7186 8.34766 21.7186 10.1406C21.7186 11.9336 21.0194 13.6211 19.7499 14.8906C18.4803 16.1602 16.7928 16.8594 14.9999 16.8594Z"
                                fill="#E8392E"></path>
                        </svg>
                        <p></p>
                        <b>
                            Đối tượng
                        </b>
                        <p></p>
                        Độ tuổi: 6-10. Lần đầu tiếp xúc với lập trình
                    </div>
                    <div class="col-6 col-lg-3 text-center px-1 pt-4">
                        <svg width="32" height="32" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M16.9834 0.333374C7.78337 0.333374 0.333374 7.80004 0.333374 17C0.333374 26.2 7.78337 33.6667 16.9834 33.6667C26.2 33.6667 33.6667 26.2 33.6667 17C33.6667 7.80004 26.2 0.333374 16.9834 0.333374ZM17 30.3334C9.63337 30.3334 3.66671 24.3667 3.66671 17C3.66671 9.63337 9.63337 3.66671 17 3.66671C24.3667 3.66671 30.3334 9.63337 30.3334 17C30.3334 24.3667 24.3667 30.3334 17 30.3334Z"
                                fill="#E8392E"></path>
                            <path
                                d="M17.8334 8.66669H15.3334V18.6667L24.0834 23.9167L25.3334 21.8667L17.8334 17.4167V8.66669Z"
                                fill="#E8392E"></path>
                        </svg>
                        <p></p>
                        <b>Thời gian học</b>
                        <p></p>
                        2 tiếng/buổi
                    </div>
                    <div class="col-6 col-lg-3 text-center px-1 pt-4">
                        <svg width="32" height="32" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M16.9999 20.3333C17.3296 20.3333 17.6518 20.2356 17.9259 20.0524C18.2 19.8693 18.4136 19.609 18.5397 19.3045C18.6659 18.9999 18.6989 18.6648 18.6346 18.3415C18.5703 18.0182 18.4115 17.7212 18.1784 17.4881C17.9453 17.255 17.6484 17.0963 17.3251 17.032C17.0018 16.9677 16.6667 17.0007 16.3621 17.1268C16.0576 17.253 15.7973 17.4666 15.6141 17.7407C15.431 18.0148 15.3333 18.337 15.3333 18.6666C15.3333 19.1087 15.5088 19.5326 15.8214 19.8452C16.134 20.1577 16.5579 20.3333 16.9999 20.3333ZM25.3333 20.3333C25.6629 20.3333 25.9851 20.2356 26.2592 20.0524C26.5333 19.8693 26.7469 19.609 26.8731 19.3045C26.9992 18.9999 27.0322 18.6648 26.9679 18.3415C26.9036 18.0182 26.7449 17.7212 26.5118 17.4881C26.2787 17.255 25.9817 17.0963 25.6584 17.032C25.3351 16.9677 25 17.0007 24.6954 17.1268C24.3909 17.253 24.1306 17.4666 23.9475 17.7407C23.7643 18.0148 23.6666 18.337 23.6666 18.6666C23.6666 19.1087 23.8422 19.5326 24.1547 19.8452C24.4673 20.1577 24.8912 20.3333 25.3333 20.3333ZM16.9999 27C17.3296 27 17.6518 26.9022 17.9259 26.7191C18.2 26.536 18.4136 26.2757 18.5397 25.9711C18.6659 25.6666 18.6989 25.3315 18.6346 25.0082C18.5703 24.6849 18.4115 24.3879 18.1784 24.1548C17.9453 23.9217 17.6484 23.763 17.3251 23.6987C17.0018 23.6344 16.6667 23.6674 16.3621 23.7935C16.0576 23.9197 15.7973 24.1333 15.6141 24.4074C15.431 24.6814 15.3333 25.0037 15.3333 25.3333C15.3333 25.7753 15.5088 26.1993 15.8214 26.5118C16.134 26.8244 16.5579 27 16.9999 27ZM25.3333 27C25.6629 27 25.9851 26.9022 26.2592 26.7191C26.5333 26.536 26.7469 26.2757 26.8731 25.9711C26.9992 25.6666 27.0322 25.3315 26.9679 25.0082C26.9036 24.6849 26.7449 24.3879 26.5118 24.1548C26.2787 23.9217 25.9817 23.763 25.6584 23.6987C25.3351 23.6344 25 23.6674 24.6954 23.7935C24.3909 23.9197 24.1306 24.1333 23.9475 24.4074C23.7643 24.6814 23.6666 25.0037 23.6666 25.3333C23.6666 25.7753 23.8422 26.1993 24.1547 26.5118C24.4673 26.8244 24.8912 27 25.3333 27ZM8.66659 20.3333C8.99622 20.3333 9.31845 20.2356 9.59254 20.0524C9.86662 19.8693 10.0802 19.609 10.2064 19.3045C10.3325 18.9999 10.3655 18.6648 10.3012 18.3415C10.2369 18.0182 10.0782 17.7212 9.8451 17.4881C9.61201 17.255 9.31504 17.0963 8.99174 17.032C8.66843 16.9677 8.33332 17.0007 8.02878 17.1268C7.72424 17.253 7.46394 17.4666 7.2808 17.7407C7.09767 18.0148 6.99992 18.337 6.99992 18.6666C6.99992 19.1087 7.17551 19.5326 7.48807 19.8452C7.80064 20.1577 8.22456 20.3333 8.66659 20.3333ZM28.6666 3.66665H26.9999V1.99998C26.9999 1.55795 26.8243 1.13403 26.5118 0.821468C26.1992 0.508908 25.7753 0.333313 25.3333 0.333313C24.8912 0.333313 24.4673 0.508908 24.1547 0.821468C23.8422 1.13403 23.6666 1.55795 23.6666 1.99998V3.66665H10.3333V1.99998C10.3333 1.55795 10.1577 1.13403 9.8451 0.821468C9.53254 0.508908 9.10861 0.333313 8.66659 0.333313C8.22456 0.333313 7.80064 0.508908 7.48807 0.821468C7.17551 1.13403 6.99992 1.55795 6.99992 1.99998V3.66665H5.33325C4.00717 3.66665 2.7354 4.19343 1.79772 5.13111C0.860036 6.06879 0.333252 7.34056 0.333252 8.66665V28.6666C0.333252 29.9927 0.860036 31.2645 1.79772 32.2022C2.7354 33.1399 4.00717 33.6666 5.33325 33.6666H28.6666C29.9927 33.6666 31.2644 33.1399 32.2021 32.2022C33.1398 31.2645 33.6666 29.9927 33.6666 28.6666V8.66665C33.6666 7.34056 33.1398 6.06879 32.2021 5.13111C31.2644 4.19343 29.9927 3.66665 28.6666 3.66665ZM30.3333 28.6666C30.3333 29.1087 30.1577 29.5326 29.8451 29.8452C29.5325 30.1577 29.1086 30.3333 28.6666 30.3333H5.33325C4.89122 30.3333 4.4673 30.1577 4.15474 29.8452C3.84218 29.5326 3.66659 29.1087 3.66659 28.6666V13.6666H30.3333V28.6666ZM30.3333 10.3333H3.66659V8.66665C3.66659 8.22462 3.84218 7.8007 4.15474 7.48814C4.4673 7.17557 4.89122 6.99998 5.33325 6.99998H28.6666C29.1086 6.99998 29.5325 7.17557 29.8451 7.48814C30.1577 7.8007 30.3333 8.22462 30.3333 8.66665V10.3333ZM8.66659 27C8.99622 27 9.31845 26.9022 9.59254 26.7191C9.86662 26.536 10.0802 26.2757 10.2064 25.9711C10.3325 25.6666 10.3655 25.3315 10.3012 25.0082C10.2369 24.6849 10.0782 24.3879 9.8451 24.1548C9.61201 23.9217 9.31504 23.763 8.99174 23.6987C8.66843 23.6344 8.33332 23.6674 8.02878 23.7935C7.72424 23.9197 7.46394 24.1333 7.2808 24.4074C7.09767 24.6814 6.99992 25.0037 6.99992 25.3333C6.99992 25.7753 7.17551 26.1993 7.48807 26.5118C7.80064 26.8244 8.22456 27 8.66659 27Z"
                                fill="#E8392E"></path>
                        </svg>
                        <p></p>
                        <b>Số buổi</b>
                        <p></p>
                        10 buổi học
                    </div>
                    <div class="col-6 col-lg-3 text-center px-1 pt-4">
                        <svg width="36" height="36" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M20 9.72504L19.6088 9.84379L3.98377 15.0788L0.431274 16.25L2.50002 16.9125V27.85C1.75377 28.2838 1.25002 29.075 1.25002 30C1.25002 30.6631 1.51342 31.299 1.98226 31.7678C2.4511 32.2366 3.08698 32.5 3.75002 32.5C4.41307 32.5 5.04895 32.2366 5.51779 31.7678C5.98663 31.299 6.25002 30.6631 6.25002 30C6.25002 29.075 5.74627 28.2838 5.00002 27.85V17.775L7.50002 18.5938V25C7.50002 26.025 8.12502 26.875 8.86752 27.4625C9.61002 28.0463 10.5325 28.4588 11.64 28.8288C13.8575 29.5663 16.78 30 20 30C23.22 30 26.1425 29.5675 28.36 28.8275C29.4675 28.4588 30.39 28.0463 31.1325 27.4613C31.875 26.875 32.5 26.025 32.5 25V18.5938L36.0163 17.4213L39.5688 16.25L36.015 15.0775L20.39 9.84379L20 9.72504ZM20 12.3438L31.7188 16.25L20 20.1563L8.28127 16.25L20 12.3438ZM10 19.4538L19.61 22.6563L20 22.7738L20.3913 22.655L30 19.4525V25C30 25.0125 30.005 25.1575 29.6088 25.4688C29.2138 25.7813 28.505 26.175 27.5775 26.485C25.725 27.1013 22.9938 27.5 20 27.5C17.0063 27.5 14.275 27.1025 12.4213 26.4838C11.4963 26.175 10.7863 25.78 10.3913 25.4688C9.99377 25.1563 10 25.0125 10 25V19.4525V19.4538Z"
                                fill="#E8392E"></path>
                        </svg>
                        <p></p>
                        <b>Số học viên</b>
                        <p></p>
                        8 - 10 bạn
                    </div>
                </div>
            </div>
        </div>

        <div class="accordion1 px-3 py-3">
            <div class="accordion-item active">
                <div>
                    <h3>Mục tiêu: </h3>
                    <p>

                            - Các em sẽ làm quen với lập trình cơ bản và các thẻ lệnh cơ bản: lệnh Motion, lệnh Looks,
                            lệnh Loop, làm quen với cách tư duy logic theo cách lập trình bằng cách chia nhỏ vấn đề, sắp
                            xếp các khối câu lệnh&nbsp;
                            <br>
                            - Chương trình sẽ bao gồm các phần cơ bản và các câu hỏi, bài tập mở rộng củng cố kiến thức
                            trong khi học.
                            <br>
                            - Mỗi bài học sẽ là một game cơ bản, kèm theo là 5-10 câu hỏi nâng cao trong khi học để giúp
                            học sinh củng cố vững chắc kiến thức.
                            <br>
                            - Các em sẽ hiểu rõ cơ chế lập trình trình, không chỉ là bắt chước làm theo để xong một
                            chương trình, mà sẽ có các
                            tình huống giả định thay đổi để hiểu rõ về những gì mình đã làm. Từ đó các em có thể linh
                            hoạt ứng dụng khi có những bài toán mới.

                    </p>
                </div>

                <div class="container">
                    <div style="max-width: 1000px; margin: 0 auto ;">

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="title">
                                    <div class="title-text txt-center" data-code-pos='ppp16973656194851'>
                                        <h3>Danh sách bài học</h3>
                                    </div>
                                </div>
                                <div class="description" style="">
                                    <div>

                                        <?php

                                        $str = "
Buổi 1: Giới thiệu về Scratch
Buổi 2: Lập trình game Jumbo Fire Dash (p1)
Buổi 3: Lập trình game Jumbo Fire Dash (p2)
Buổi 4: Lập trình game Jumbo Fire Dash (p3)
Buổi 5: Workshop thuyết trình
Buổi 6: Lập trình game The end of Jurassic (phần 1)
Buổi 7: Lập trình game The end of Jurassic (phần 2)
Buổi 8: Lập trình game The end of Jurassic (phần 3)
Buổi 9: Lập trình game FlappyBird
Buổi 10: Lập trình game Xe tăng - Cá sấu
";
//
//- Vẽ cầu vồng
//- Game thu hoạch trái cây
//- Mèo đuổi chuột
//- Cá lớn nuốt cá bé
//- Máy bay chinh phục thiên hà
//- Game Pingpong
//- 2 xe bắn nhau đạn cầu vồng tránh nhau
////- 2 xe tăng bắn nhau trên mặt phẳng, 2 người chơi
//- Trò chơi đập bóng
//- Mario vượt chướng ngại vật
//- Game 2 người thi tính toán
//- Game chơi cờ caro
//- Flappy Bird, biến thể thành Flappy Fish
//- Xe tăng cá sấu
//- Đua xe 2 người cùng chơi
//- Bắn pháo hoa
                                        $m1 = explode("\n", $str);
                                        echo "<ul>";
                                        foreach ($m1 AS $line) {

                                            $line = trim($line);

                                            if (!$line || $line[0] == '/')
                                                continue;
                                            $line = trim($line, " -");
                                            echo "\n <li> $line </li>";
                                        }
                                        echo "</ul>";
                                        ?>

                                    </div>
                                    <div></div>
                                </div>


                            </div>
                            <div class="col-sm-6 txt-center pt-5">

                                <h3>Học Phí </h3>

                                <div
                                    style="background-color: midnightblue; color: white; padding: 10px 20px; width: 180px; margin: 10px auto; font-size: larger">
                                    2.000.000 VNĐ
                                </div>
                                (10 buổi học, 2h/1 buổi)

                            </div>

                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>

    <div class="container pt-2 mt-3  flex1" data-code-pos='ppp16857567667949'
         style="">
        <div class="pt-2 pb-1 text-center text-white header_topic" style="">

            <div style="position: absolute; top: -100px; color: transparent"><a id="sadvance" style="color: red">  </a> </div>
            <h2>Lập trình Scratch nâng cao</h2>

        </div>

        <div class="container-fluid pt-2 pb-4 bg-light qqqq1111" data-code-pos='ppp16866667949'>
            <div class="container">
                <div class="row">
                    <div class="col-6 col-lg-3 text-center px-1 pt-4">
                        <svg width="32" height="32" viewBox="0 0 30 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M28.535 25.8281C27.7981 24.0827 26.7288 22.4973 25.3866 21.1602C24.0484 19.8192 22.4633 18.75 20.7186 18.0117C20.703 18.0039 20.6874 18 20.6717 17.9922C23.1053 16.2344 24.6874 13.3711 24.6874 10.1406C24.6874 4.78906 20.3514 0.453125 14.9999 0.453125C9.64829 0.453125 5.31235 4.78906 5.31235 10.1406C5.31235 13.3711 6.89438 16.2344 9.32798 17.9961C9.31235 18.0039 9.29673 18.0078 9.2811 18.0156C7.5311 18.7539 5.96079 19.8125 4.61314 21.1641C3.27216 22.5022 2.203 24.0874 1.4647 25.832C0.739393 27.5401 0.34822 29.3713 0.312354 31.2266C0.311311 31.2683 0.318624 31.3097 0.333861 31.3486C0.349098 31.3874 0.371951 31.4228 0.401074 31.4526C0.430197 31.4825 0.465 31.5062 0.503432 31.5224C0.541865 31.5386 0.58315 31.5469 0.624854 31.5469H2.9686C3.14048 31.5469 3.2772 31.4102 3.2811 31.2422C3.35923 28.2266 4.57017 25.4023 6.71079 23.2617C8.92563 21.0469 11.867 19.8281 14.9999 19.8281C18.1327 19.8281 21.0741 21.0469 23.2889 23.2617C25.4295 25.4023 26.6405 28.2266 26.7186 31.2422C26.7225 31.4141 26.8592 31.5469 27.0311 31.5469H29.3749C29.4166 31.5469 29.4578 31.5386 29.4963 31.5224C29.5347 31.5062 29.5695 31.4825 29.5986 31.4526C29.6278 31.4228 29.6506 31.3874 29.6658 31.3486C29.6811 31.3097 29.6884 31.2683 29.6874 31.2266C29.6483 29.3594 29.2616 27.543 28.535 25.8281ZM14.9999 16.8594C13.2069 16.8594 11.5194 16.1602 10.2499 14.8906C8.98032 13.6211 8.2811 11.9336 8.2811 10.1406C8.2811 8.34766 8.98032 6.66016 10.2499 5.39062C11.5194 4.12109 13.2069 3.42188 14.9999 3.42188C16.7928 3.42188 18.4803 4.12109 19.7499 5.39062C21.0194 6.66016 21.7186 8.34766 21.7186 10.1406C21.7186 11.9336 21.0194 13.6211 19.7499 14.8906C18.4803 16.1602 16.7928 16.8594 14.9999 16.8594Z"
                                fill="#E8392E"></path>
                        </svg>
                        <p></p>
                        <b>
                            Đối tượng
                        </b>
                        <p></p>
                        Độ tuổi: 8-10. Lần đầu tiếp xúc với lập trình
                    </div>
                    <div class="col-6 col-lg-3 text-center px-1 pt-4">
                        <svg width="32" height="32" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M16.9834 0.333374C7.78337 0.333374 0.333374 7.80004 0.333374 17C0.333374 26.2 7.78337 33.6667 16.9834 33.6667C26.2 33.6667 33.6667 26.2 33.6667 17C33.6667 7.80004 26.2 0.333374 16.9834 0.333374ZM17 30.3334C9.63337 30.3334 3.66671 24.3667 3.66671 17C3.66671 9.63337 9.63337 3.66671 17 3.66671C24.3667 3.66671 30.3334 9.63337 30.3334 17C30.3334 24.3667 24.3667 30.3334 17 30.3334Z"
                                fill="#E8392E"></path>
                            <path
                                d="M17.8334 8.66669H15.3334V18.6667L24.0834 23.9167L25.3334 21.8667L17.8334 17.4167V8.66669Z"
                                fill="#E8392E"></path>
                        </svg>
                        <p></p>
                        <b>Thời gian học</b>
                        <p></p>
                        2 tiếng/buổi
                    </div>
                    <div class="col-6 col-lg-3 text-center px-1 pt-4">
                        <svg width="32" height="32" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M16.9999 20.3333C17.3296 20.3333 17.6518 20.2356 17.9259 20.0524C18.2 19.8693 18.4136 19.609 18.5397 19.3045C18.6659 18.9999 18.6989 18.6648 18.6346 18.3415C18.5703 18.0182 18.4115 17.7212 18.1784 17.4881C17.9453 17.255 17.6484 17.0963 17.3251 17.032C17.0018 16.9677 16.6667 17.0007 16.3621 17.1268C16.0576 17.253 15.7973 17.4666 15.6141 17.7407C15.431 18.0148 15.3333 18.337 15.3333 18.6666C15.3333 19.1087 15.5088 19.5326 15.8214 19.8452C16.134 20.1577 16.5579 20.3333 16.9999 20.3333ZM25.3333 20.3333C25.6629 20.3333 25.9851 20.2356 26.2592 20.0524C26.5333 19.8693 26.7469 19.609 26.8731 19.3045C26.9992 18.9999 27.0322 18.6648 26.9679 18.3415C26.9036 18.0182 26.7449 17.7212 26.5118 17.4881C26.2787 17.255 25.9817 17.0963 25.6584 17.032C25.3351 16.9677 25 17.0007 24.6954 17.1268C24.3909 17.253 24.1306 17.4666 23.9475 17.7407C23.7643 18.0148 23.6666 18.337 23.6666 18.6666C23.6666 19.1087 23.8422 19.5326 24.1547 19.8452C24.4673 20.1577 24.8912 20.3333 25.3333 20.3333ZM16.9999 27C17.3296 27 17.6518 26.9022 17.9259 26.7191C18.2 26.536 18.4136 26.2757 18.5397 25.9711C18.6659 25.6666 18.6989 25.3315 18.6346 25.0082C18.5703 24.6849 18.4115 24.3879 18.1784 24.1548C17.9453 23.9217 17.6484 23.763 17.3251 23.6987C17.0018 23.6344 16.6667 23.6674 16.3621 23.7935C16.0576 23.9197 15.7973 24.1333 15.6141 24.4074C15.431 24.6814 15.3333 25.0037 15.3333 25.3333C15.3333 25.7753 15.5088 26.1993 15.8214 26.5118C16.134 26.8244 16.5579 27 16.9999 27ZM25.3333 27C25.6629 27 25.9851 26.9022 26.2592 26.7191C26.5333 26.536 26.7469 26.2757 26.8731 25.9711C26.9992 25.6666 27.0322 25.3315 26.9679 25.0082C26.9036 24.6849 26.7449 24.3879 26.5118 24.1548C26.2787 23.9217 25.9817 23.763 25.6584 23.6987C25.3351 23.6344 25 23.6674 24.6954 23.7935C24.3909 23.9197 24.1306 24.1333 23.9475 24.4074C23.7643 24.6814 23.6666 25.0037 23.6666 25.3333C23.6666 25.7753 23.8422 26.1993 24.1547 26.5118C24.4673 26.8244 24.8912 27 25.3333 27ZM8.66659 20.3333C8.99622 20.3333 9.31845 20.2356 9.59254 20.0524C9.86662 19.8693 10.0802 19.609 10.2064 19.3045C10.3325 18.9999 10.3655 18.6648 10.3012 18.3415C10.2369 18.0182 10.0782 17.7212 9.8451 17.4881C9.61201 17.255 9.31504 17.0963 8.99174 17.032C8.66843 16.9677 8.33332 17.0007 8.02878 17.1268C7.72424 17.253 7.46394 17.4666 7.2808 17.7407C7.09767 18.0148 6.99992 18.337 6.99992 18.6666C6.99992 19.1087 7.17551 19.5326 7.48807 19.8452C7.80064 20.1577 8.22456 20.3333 8.66659 20.3333ZM28.6666 3.66665H26.9999V1.99998C26.9999 1.55795 26.8243 1.13403 26.5118 0.821468C26.1992 0.508908 25.7753 0.333313 25.3333 0.333313C24.8912 0.333313 24.4673 0.508908 24.1547 0.821468C23.8422 1.13403 23.6666 1.55795 23.6666 1.99998V3.66665H10.3333V1.99998C10.3333 1.55795 10.1577 1.13403 9.8451 0.821468C9.53254 0.508908 9.10861 0.333313 8.66659 0.333313C8.22456 0.333313 7.80064 0.508908 7.48807 0.821468C7.17551 1.13403 6.99992 1.55795 6.99992 1.99998V3.66665H5.33325C4.00717 3.66665 2.7354 4.19343 1.79772 5.13111C0.860036 6.06879 0.333252 7.34056 0.333252 8.66665V28.6666C0.333252 29.9927 0.860036 31.2645 1.79772 32.2022C2.7354 33.1399 4.00717 33.6666 5.33325 33.6666H28.6666C29.9927 33.6666 31.2644 33.1399 32.2021 32.2022C33.1398 31.2645 33.6666 29.9927 33.6666 28.6666V8.66665C33.6666 7.34056 33.1398 6.06879 32.2021 5.13111C31.2644 4.19343 29.9927 3.66665 28.6666 3.66665ZM30.3333 28.6666C30.3333 29.1087 30.1577 29.5326 29.8451 29.8452C29.5325 30.1577 29.1086 30.3333 28.6666 30.3333H5.33325C4.89122 30.3333 4.4673 30.1577 4.15474 29.8452C3.84218 29.5326 3.66659 29.1087 3.66659 28.6666V13.6666H30.3333V28.6666ZM30.3333 10.3333H3.66659V8.66665C3.66659 8.22462 3.84218 7.8007 4.15474 7.48814C4.4673 7.17557 4.89122 6.99998 5.33325 6.99998H28.6666C29.1086 6.99998 29.5325 7.17557 29.8451 7.48814C30.1577 7.8007 30.3333 8.22462 30.3333 8.66665V10.3333ZM8.66659 27C8.99622 27 9.31845 26.9022 9.59254 26.7191C9.86662 26.536 10.0802 26.2757 10.2064 25.9711C10.3325 25.6666 10.3655 25.3315 10.3012 25.0082C10.2369 24.6849 10.0782 24.3879 9.8451 24.1548C9.61201 23.9217 9.31504 23.763 8.99174 23.6987C8.66843 23.6344 8.33332 23.6674 8.02878 23.7935C7.72424 23.9197 7.46394 24.1333 7.2808 24.4074C7.09767 24.6814 6.99992 25.0037 6.99992 25.3333C6.99992 25.7753 7.17551 26.1993 7.48807 26.5118C7.80064 26.8244 8.22456 27 8.66659 27Z"
                                fill="#E8392E"></path>
                        </svg>
                        <p></p>
                        <b>Số buổi</b>
                        <p></p>
                        10 buổi học
                    </div>
                    <div class="col-6 col-lg-3 text-center px-1 pt-4">
                        <svg width="36" height="36" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M20 9.72504L19.6088 9.84379L3.98377 15.0788L0.431274 16.25L2.50002 16.9125V27.85C1.75377 28.2838 1.25002 29.075 1.25002 30C1.25002 30.6631 1.51342 31.299 1.98226 31.7678C2.4511 32.2366 3.08698 32.5 3.75002 32.5C4.41307 32.5 5.04895 32.2366 5.51779 31.7678C5.98663 31.299 6.25002 30.6631 6.25002 30C6.25002 29.075 5.74627 28.2838 5.00002 27.85V17.775L7.50002 18.5938V25C7.50002 26.025 8.12502 26.875 8.86752 27.4625C9.61002 28.0463 10.5325 28.4588 11.64 28.8288C13.8575 29.5663 16.78 30 20 30C23.22 30 26.1425 29.5675 28.36 28.8275C29.4675 28.4588 30.39 28.0463 31.1325 27.4613C31.875 26.875 32.5 26.025 32.5 25V18.5938L36.0163 17.4213L39.5688 16.25L36.015 15.0775L20.39 9.84379L20 9.72504ZM20 12.3438L31.7188 16.25L20 20.1563L8.28127 16.25L20 12.3438ZM10 19.4538L19.61 22.6563L20 22.7738L20.3913 22.655L30 19.4525V25C30 25.0125 30.005 25.1575 29.6088 25.4688C29.2138 25.7813 28.505 26.175 27.5775 26.485C25.725 27.1013 22.9938 27.5 20 27.5C17.0063 27.5 14.275 27.1025 12.4213 26.4838C11.4963 26.175 10.7863 25.78 10.3913 25.4688C9.99377 25.1563 10 25.0125 10 25V19.4525V19.4538Z"
                                fill="#E8392E"></path>
                        </svg>
                        <p></p>
                        <b>Số học viên</b>
                        <p></p>
                        8 - 10 bạn
                    </div>
                </div>
            </div>
        </div>

        <div class="accordion1 px-3 py-3">
            <div class="accordion-item active">
                <div>
                    <h3>Mục tiêu: </h3>
                    <p>

                            - Các em sẽ làm quen với lập trình nâng cao gồm các kiến thức từ cơ bản và mở rộng, kế thừa sang các bài toán phức tạp hơn.

                    </p>
                </div>

                <div class="container">
                    <div style="max-width: 1000px; margin: 0 auto ;">

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="title">
                                    <div class="title-text txt-center" data-code-pos='ppp16973656194851'>
                                        <h3>Danh sách bài học</h3>
                                    </div>
                                </div>
                                <div class="description" style="">
                                    <div>

                                        <?php

                                        $str = "
Buổi 1: Dự án Quizziz (Phần 1)
Buổi 2: Dự án Quizziz (Phần 2)
Buổi 3: Dự án Quizziz (Phần 3)
Buổi 4: Dự án Quizziz (Phần 4)
Buổi 5: Workshop kỹ năng
Buổi 6: Math Racing (Phần 1)
Buổi 7: Math Racing (Phần 2)
Buổi 8: Math Racing (Phần 3)
Buổi 9: Lên ý tưởng và định hướng sản phẩm
Buổi 10: Thực hành hoàn thiện sản phẩm cuối khóa
";
                                        $m1 = explode("\n", $str);
                                        echo "<ul>";
                                        foreach ($m1 AS $line) {

                                            $line = trim($line);

                                            if (!$line || $line[0] == '/')
                                                continue;
                                            $line = trim($line, " -");
                                            echo "\n <li> $line </li>";
                                        }
                                        echo "</ul>";
                                        ?>

                                    </div>
                                    <div></div>
                                </div>


                            </div>
                            <div class="col-sm-6 txt-center pt-5">

                                <h3>Học Phí </h3>

                                <div
                                    style="background-color: midnightblue; color: white; padding: 10px 20px; width: 180px; margin: 10px auto; font-size: larger">
                                    2.000.000 VNĐ
                                </div>
                                (10 buổi học, 2h/1 buổi)

                            </div>

                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>

    <div class="container pt-2 mt-3  flex1" data-code-pos='ppp16857567667949'
         style="display:  none">

        <div class="pt-2 pb-1 text-center text-white" style="">
            <div style="position: absolute; top: -100px; color: transparent"><a id="srobot" style="color: red">  </a> </div>
            <h2>Lập trình Robotic</h2>

        </div>
        <div class="accordion1 px-3 py-3">
            <div class="accordion-item active">
                <div class="title">
                    <div class="title-text" data-code-pos='ppp16973656231621'>
                        <h3>Danh sách bài học</h3>
                    </div>
                </div>
                <div class="description" style="">
                    <div>
                        <p>
                            <strong>
                                <em>
                                    Đang xây dựng
                                </em>
                            </strong>
                        </p>

                        Đang xây dựng

                    </div>
                    <div></div>
                </div>
            </div>

        </div>
    </div>


    <div class="container pt-2 mt-3  flex1" data-code-pos='ppp16857567667949'
         style="">

        <div class="pt-2 pb-1 text-center text-white header_topic" style="">
            <div style="position: absolute; top: -100px; color: transparent"><a id="spython" style="color: red">  </a> </div>
            <h2>Lập trình Game với Python</h2>

        </div>

        <div class="container-fluid pt-2 pb-4 bg-light qqqq1111" data-code-pos='ppp16866667949'>
            <div class="container">
                <div class="row">
                    <div class="col-6 col-lg-3 text-center px-1 pt-4">
                        <svg width="32" height="32" viewBox="0 0 30 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M28.535 25.8281C27.7981 24.0827 26.7288 22.4973 25.3866 21.1602C24.0484 19.8192 22.4633 18.75 20.7186 18.0117C20.703 18.0039 20.6874 18 20.6717 17.9922C23.1053 16.2344 24.6874 13.3711 24.6874 10.1406C24.6874 4.78906 20.3514 0.453125 14.9999 0.453125C9.64829 0.453125 5.31235 4.78906 5.31235 10.1406C5.31235 13.3711 6.89438 16.2344 9.32798 17.9961C9.31235 18.0039 9.29673 18.0078 9.2811 18.0156C7.5311 18.7539 5.96079 19.8125 4.61314 21.1641C3.27216 22.5022 2.203 24.0874 1.4647 25.832C0.739393 27.5401 0.34822 29.3713 0.312354 31.2266C0.311311 31.2683 0.318624 31.3097 0.333861 31.3486C0.349098 31.3874 0.371951 31.4228 0.401074 31.4526C0.430197 31.4825 0.465 31.5062 0.503432 31.5224C0.541865 31.5386 0.58315 31.5469 0.624854 31.5469H2.9686C3.14048 31.5469 3.2772 31.4102 3.2811 31.2422C3.35923 28.2266 4.57017 25.4023 6.71079 23.2617C8.92563 21.0469 11.867 19.8281 14.9999 19.8281C18.1327 19.8281 21.0741 21.0469 23.2889 23.2617C25.4295 25.4023 26.6405 28.2266 26.7186 31.2422C26.7225 31.4141 26.8592 31.5469 27.0311 31.5469H29.3749C29.4166 31.5469 29.4578 31.5386 29.4963 31.5224C29.5347 31.5062 29.5695 31.4825 29.5986 31.4526C29.6278 31.4228 29.6506 31.3874 29.6658 31.3486C29.6811 31.3097 29.6884 31.2683 29.6874 31.2266C29.6483 29.3594 29.2616 27.543 28.535 25.8281ZM14.9999 16.8594C13.2069 16.8594 11.5194 16.1602 10.2499 14.8906C8.98032 13.6211 8.2811 11.9336 8.2811 10.1406C8.2811 8.34766 8.98032 6.66016 10.2499 5.39062C11.5194 4.12109 13.2069 3.42188 14.9999 3.42188C16.7928 3.42188 18.4803 4.12109 19.7499 5.39062C21.0194 6.66016 21.7186 8.34766 21.7186 10.1406C21.7186 11.9336 21.0194 13.6211 19.7499 14.8906C18.4803 16.1602 16.7928 16.8594 14.9999 16.8594Z"
                                fill="#E8392E"></path>
                        </svg>
                        <p></p>
                        <b>
                            Đối tượng
                        </b>
                        <p></p>
                        Độ tuổi: 12-16. Các em học sinh cấp 2 bắt đầu học lập trình
                    </div>

                    <div class="col-6 col-lg-3 text-center px-1 pt-4">
                        <svg width="32" height="32" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M16.9834 0.333374C7.78337 0.333374 0.333374 7.80004 0.333374 17C0.333374 26.2 7.78337 33.6667 16.9834 33.6667C26.2 33.6667 33.6667 26.2 33.6667 17C33.6667 7.80004 26.2 0.333374 16.9834 0.333374ZM17 30.3334C9.63337 30.3334 3.66671 24.3667 3.66671 17C3.66671 9.63337 9.63337 3.66671 17 3.66671C24.3667 3.66671 30.3334 9.63337 30.3334 17C30.3334 24.3667 24.3667 30.3334 17 30.3334Z"
                                fill="#E8392E"></path>
                            <path
                                d="M17.8334 8.66669H15.3334V18.6667L24.0834 23.9167L25.3334 21.8667L17.8334 17.4167V8.66669Z"
                                fill="#E8392E"></path>
                        </svg>
                        <p></p>
                        <b>Thời gian học</b>
                        <p></p>
                        2 tiếng/buổi
                    </div>

                    <div class="col-6 col-lg-3 text-center px-1 pt-4">
                        <svg width="32" height="32" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M16.9999 20.3333C17.3296 20.3333 17.6518 20.2356 17.9259 20.0524C18.2 19.8693 18.4136 19.609 18.5397 19.3045C18.6659 18.9999 18.6989 18.6648 18.6346 18.3415C18.5703 18.0182 18.4115 17.7212 18.1784 17.4881C17.9453 17.255 17.6484 17.0963 17.3251 17.032C17.0018 16.9677 16.6667 17.0007 16.3621 17.1268C16.0576 17.253 15.7973 17.4666 15.6141 17.7407C15.431 18.0148 15.3333 18.337 15.3333 18.6666C15.3333 19.1087 15.5088 19.5326 15.8214 19.8452C16.134 20.1577 16.5579 20.3333 16.9999 20.3333ZM25.3333 20.3333C25.6629 20.3333 25.9851 20.2356 26.2592 20.0524C26.5333 19.8693 26.7469 19.609 26.8731 19.3045C26.9992 18.9999 27.0322 18.6648 26.9679 18.3415C26.9036 18.0182 26.7449 17.7212 26.5118 17.4881C26.2787 17.255 25.9817 17.0963 25.6584 17.032C25.3351 16.9677 25 17.0007 24.6954 17.1268C24.3909 17.253 24.1306 17.4666 23.9475 17.7407C23.7643 18.0148 23.6666 18.337 23.6666 18.6666C23.6666 19.1087 23.8422 19.5326 24.1547 19.8452C24.4673 20.1577 24.8912 20.3333 25.3333 20.3333ZM16.9999 27C17.3296 27 17.6518 26.9022 17.9259 26.7191C18.2 26.536 18.4136 26.2757 18.5397 25.9711C18.6659 25.6666 18.6989 25.3315 18.6346 25.0082C18.5703 24.6849 18.4115 24.3879 18.1784 24.1548C17.9453 23.9217 17.6484 23.763 17.3251 23.6987C17.0018 23.6344 16.6667 23.6674 16.3621 23.7935C16.0576 23.9197 15.7973 24.1333 15.6141 24.4074C15.431 24.6814 15.3333 25.0037 15.3333 25.3333C15.3333 25.7753 15.5088 26.1993 15.8214 26.5118C16.134 26.8244 16.5579 27 16.9999 27ZM25.3333 27C25.6629 27 25.9851 26.9022 26.2592 26.7191C26.5333 26.536 26.7469 26.2757 26.8731 25.9711C26.9992 25.6666 27.0322 25.3315 26.9679 25.0082C26.9036 24.6849 26.7449 24.3879 26.5118 24.1548C26.2787 23.9217 25.9817 23.763 25.6584 23.6987C25.3351 23.6344 25 23.6674 24.6954 23.7935C24.3909 23.9197 24.1306 24.1333 23.9475 24.4074C23.7643 24.6814 23.6666 25.0037 23.6666 25.3333C23.6666 25.7753 23.8422 26.1993 24.1547 26.5118C24.4673 26.8244 24.8912 27 25.3333 27ZM8.66659 20.3333C8.99622 20.3333 9.31845 20.2356 9.59254 20.0524C9.86662 19.8693 10.0802 19.609 10.2064 19.3045C10.3325 18.9999 10.3655 18.6648 10.3012 18.3415C10.2369 18.0182 10.0782 17.7212 9.8451 17.4881C9.61201 17.255 9.31504 17.0963 8.99174 17.032C8.66843 16.9677 8.33332 17.0007 8.02878 17.1268C7.72424 17.253 7.46394 17.4666 7.2808 17.7407C7.09767 18.0148 6.99992 18.337 6.99992 18.6666C6.99992 19.1087 7.17551 19.5326 7.48807 19.8452C7.80064 20.1577 8.22456 20.3333 8.66659 20.3333ZM28.6666 3.66665H26.9999V1.99998C26.9999 1.55795 26.8243 1.13403 26.5118 0.821468C26.1992 0.508908 25.7753 0.333313 25.3333 0.333313C24.8912 0.333313 24.4673 0.508908 24.1547 0.821468C23.8422 1.13403 23.6666 1.55795 23.6666 1.99998V3.66665H10.3333V1.99998C10.3333 1.55795 10.1577 1.13403 9.8451 0.821468C9.53254 0.508908 9.10861 0.333313 8.66659 0.333313C8.22456 0.333313 7.80064 0.508908 7.48807 0.821468C7.17551 1.13403 6.99992 1.55795 6.99992 1.99998V3.66665H5.33325C4.00717 3.66665 2.7354 4.19343 1.79772 5.13111C0.860036 6.06879 0.333252 7.34056 0.333252 8.66665V28.6666C0.333252 29.9927 0.860036 31.2645 1.79772 32.2022C2.7354 33.1399 4.00717 33.6666 5.33325 33.6666H28.6666C29.9927 33.6666 31.2644 33.1399 32.2021 32.2022C33.1398 31.2645 33.6666 29.9927 33.6666 28.6666V8.66665C33.6666 7.34056 33.1398 6.06879 32.2021 5.13111C31.2644 4.19343 29.9927 3.66665 28.6666 3.66665ZM30.3333 28.6666C30.3333 29.1087 30.1577 29.5326 29.8451 29.8452C29.5325 30.1577 29.1086 30.3333 28.6666 30.3333H5.33325C4.89122 30.3333 4.4673 30.1577 4.15474 29.8452C3.84218 29.5326 3.66659 29.1087 3.66659 28.6666V13.6666H30.3333V28.6666ZM30.3333 10.3333H3.66659V8.66665C3.66659 8.22462 3.84218 7.8007 4.15474 7.48814C4.4673 7.17557 4.89122 6.99998 5.33325 6.99998H28.6666C29.1086 6.99998 29.5325 7.17557 29.8451 7.48814C30.1577 7.8007 30.3333 8.22462 30.3333 8.66665V10.3333ZM8.66659 27C8.99622 27 9.31845 26.9022 9.59254 26.7191C9.86662 26.536 10.0802 26.2757 10.2064 25.9711C10.3325 25.6666 10.3655 25.3315 10.3012 25.0082C10.2369 24.6849 10.0782 24.3879 9.8451 24.1548C9.61201 23.9217 9.31504 23.763 8.99174 23.6987C8.66843 23.6344 8.33332 23.6674 8.02878 23.7935C7.72424 23.9197 7.46394 24.1333 7.2808 24.4074C7.09767 24.6814 6.99992 25.0037 6.99992 25.3333C6.99992 25.7753 7.17551 26.1993 7.48807 26.5118C7.80064 26.8244 8.22456 27 8.66659 27Z"
                                fill="#E8392E"></path>
                        </svg>
                        <p></p>
                        <b>Số buổi</b>
                        <p></p>
                        42 buổi học
                    </div>
                    <div class="col-6 col-lg-3 text-center px-1 pt-4">
                        <svg width="36" height="36" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M20 9.72504L19.6088 9.84379L3.98377 15.0788L0.431274 16.25L2.50002 16.9125V27.85C1.75377 28.2838 1.25002 29.075 1.25002 30C1.25002 30.6631 1.51342 31.299 1.98226 31.7678C2.4511 32.2366 3.08698 32.5 3.75002 32.5C4.41307 32.5 5.04895 32.2366 5.51779 31.7678C5.98663 31.299 6.25002 30.6631 6.25002 30C6.25002 29.075 5.74627 28.2838 5.00002 27.85V17.775L7.50002 18.5938V25C7.50002 26.025 8.12502 26.875 8.86752 27.4625C9.61002 28.0463 10.5325 28.4588 11.64 28.8288C13.8575 29.5663 16.78 30 20 30C23.22 30 26.1425 29.5675 28.36 28.8275C29.4675 28.4588 30.39 28.0463 31.1325 27.4613C31.875 26.875 32.5 26.025 32.5 25V18.5938L36.0163 17.4213L39.5688 16.25L36.015 15.0775L20.39 9.84379L20 9.72504ZM20 12.3438L31.7188 16.25L20 20.1563L8.28127 16.25L20 12.3438ZM10 19.4538L19.61 22.6563L20 22.7738L20.3913 22.655L30 19.4525V25C30 25.0125 30.005 25.1575 29.6088 25.4688C29.2138 25.7813 28.505 26.175 27.5775 26.485C25.725 27.1013 22.9938 27.5 20 27.5C17.0063 27.5 14.275 27.1025 12.4213 26.4838C11.4963 26.175 10.7863 25.78 10.3913 25.4688C9.99377 25.1563 10 25.0125 10 25V19.4525V19.4538Z"
                                fill="#E8392E"></path>
                        </svg>
                        <p></p>
                        <b>Số học viên</b>
                        <p></p>
                        8 - 10 bạn
                    </div>
                </div>
            </div>
        </div>


        <div class="accordion1 px-3 py-3">
            <div class="accordion-item active">
                <div>
                    <h3>Mục tiêu: </h3>
                    <p>

                            - Các em sẽ thành thạo lập trình python cơ bản chỉ qua một khóa học cơ bản
                            <br>
                            - Cách cài đặt python và pygame. Python ở đây sẽ sử dụng gói phần mềm Anaconda. Anaconda là
                            nền tảng mã nguồn mở về Khoa học dữ liệu trên Python thông dụng nhất hiện nay. Khi cài đặt
                            Anaconda, bạn sẽ được cài mặc định Spyder. Đây là 1 trong những IDE (môi trường tích hợp
                            dùng để phát triển phần mềm) rất tốt cho data science.
                            <br>
                            - Cơ bản về ngôn ngữ lập trình python. Bao gồm: variable, collection, control flow, function
                            và class. Chúng ta sẽ thảo luận về các biến và các phép toán. Collection là các kiểu dữ liệu
                            như mảng, tuple, dictionary, … Sau đó, control flow là các câu lệnh rẽ nhánh và các vòng
                            lặp. Sau cùng đó là những chủ đề về hàm, các lớp và đối tượng. Đó cũng là chủ đề chính trong
                            việc lập trình game, giúp game tạo ra trở nên hướng đối tượng.
                            <br>
                            - Trong khóa học, bạn không học những lý thuyết mà có thể ứng dụng ngay những gì đã học để
                            tự mình lập trình một game đơn giản (game kiểu băng qua đường) từ số không. Bạn sẽ học được
                            làm thế nào để thiết lập màn hình game, làm thế nào để đưa các đối tượng lên màn hình, làm
                            thế nào để thực hiện một vòng lặp game. Đồng thời cũng học cách điều khiển việc di chuyển
                            các đối tượng và kiểm tra sự va chạm của các đối tượng trong game.
                            <br>
                            - Cuối cùng, thông qua khóa học, các em sẽ hoàn thành được một game. Thông qua dự án cụ thể
                            như vậy, bạn sẽ nắm vững hơn về ngôn ngữ lập trình python, vững hơn về cách sử dụng các
                            biến, hàm, các cú pháp, các câu lệnh và thậm chí là các khái niệm và cách lập trình hướng
                            đối tượng trong python.

                    </p>
                </div>

                <div class="row">
                    <div class="col-sm-7">


                        <div class="title">
                            <div class="title-text" data-code-pos='ppp16973656135691'>
                                <h3>Danh sách bài học</h3>
                            </div>
                        </div>
                        <div class="description" style="">
                            <div>
                                <?php
                                $str = "
Bài 1: Giới thiệu khóa học
Bài 2: Cài đặt python và pygame
Bài 3: Giới thiệu spyder và viết một chương trình đơn giản

Bài 4: Các biến trong python
Bài 5: Các phép toán cơ bản trong python
Bài 6: Collection trong python
 Bài 7: Câu lệnh If
 Bài 8: Vòng lặp trong pygame
 Bài 9: Hàm trong python
 Bài 10: Lớp và đối tượng
 Bài 11: Lớp em và kế thừa

 Bài 12: Thiết lập các hiển thị cho game
 Bài 13: Thiết lập vòng lập game cơ bản
 Bài 14: Vẽ các đối tượng ra màn hình
 Bài 15: Lập trình game hướng đối tượng
 Bài 16: Cài đặt các lớp của game
 Bài 17: Cài đặt lớp nhân vật người chơi
 Bài 18: Cài đặt lớp quân thù và kiểm tra biên
 Bài 19: Cài đặt các kiểm tra va chạm
 Bài 20: Cài đặt các điều kiện thắng và thua
 Bài 21: Tăng độ khó của game
 Bài 22: Tổng kết khóa học
";

                                $mm = explode("\n", $str);
                                echo "\n <ul>";
                                foreach ($mm AS $line) {
                                    $line = trim($line);
                                    if (!$line)
                                        continue;
                                    echo " <li>$line</li> ";

                                }
                                echo "\n </ul>";

                                ?>

                            </div>
                            <div></div>
                        </div>

                    </div>
                    <div class="col-sm-5">
                        <div class="col-md-5 txt-center pt-3" data-code-pos='ppp16973640395041'>
                            <h3>Học Phí </h3>

                            <div
                                style="background-color: midnightblue; color: white; padding: 10px 20px; width: 180px; margin: 10px auto; font-size: larger">
                                2.000.000 VNĐ
                            </div>
                            (10 buổi học, 2h/1 buổi)
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>


    <div class="container pt-2 mt-3  flex1" data-code-pos='ppp16857567667949'
         style="">

        <div class="pt-2 pb-1 text-center text-white header_topic" style="">
            <div style="position: absolute; top: -100px; color: transparent"><a id="smath" style="color: red">  </a> </div>
            <h2>Câu lạc bộ thuật toán & lập trình</h2>

        </div>

        <div class="container-fluid pt-2 pb-4 bg-light qqqq1111" data-code-pos='ppp16866667949'>
            <div class="container">
                <div class="row">
                    <div class="col-6 col-lg-3 text-center px-1 pt-4">
                        <svg width="32" height="32" viewBox="0 0 30 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M28.535 25.8281C27.7981 24.0827 26.7288 22.4973 25.3866 21.1602C24.0484 19.8192 22.4633 18.75 20.7186 18.0117C20.703 18.0039 20.6874 18 20.6717 17.9922C23.1053 16.2344 24.6874 13.3711 24.6874 10.1406C24.6874 4.78906 20.3514 0.453125 14.9999 0.453125C9.64829 0.453125 5.31235 4.78906 5.31235 10.1406C5.31235 13.3711 6.89438 16.2344 9.32798 17.9961C9.31235 18.0039 9.29673 18.0078 9.2811 18.0156C7.5311 18.7539 5.96079 19.8125 4.61314 21.1641C3.27216 22.5022 2.203 24.0874 1.4647 25.832C0.739393 27.5401 0.34822 29.3713 0.312354 31.2266C0.311311 31.2683 0.318624 31.3097 0.333861 31.3486C0.349098 31.3874 0.371951 31.4228 0.401074 31.4526C0.430197 31.4825 0.465 31.5062 0.503432 31.5224C0.541865 31.5386 0.58315 31.5469 0.624854 31.5469H2.9686C3.14048 31.5469 3.2772 31.4102 3.2811 31.2422C3.35923 28.2266 4.57017 25.4023 6.71079 23.2617C8.92563 21.0469 11.867 19.8281 14.9999 19.8281C18.1327 19.8281 21.0741 21.0469 23.2889 23.2617C25.4295 25.4023 26.6405 28.2266 26.7186 31.2422C26.7225 31.4141 26.8592 31.5469 27.0311 31.5469H29.3749C29.4166 31.5469 29.4578 31.5386 29.4963 31.5224C29.5347 31.5062 29.5695 31.4825 29.5986 31.4526C29.6278 31.4228 29.6506 31.3874 29.6658 31.3486C29.6811 31.3097 29.6884 31.2683 29.6874 31.2266C29.6483 29.3594 29.2616 27.543 28.535 25.8281ZM14.9999 16.8594C13.2069 16.8594 11.5194 16.1602 10.2499 14.8906C8.98032 13.6211 8.2811 11.9336 8.2811 10.1406C8.2811 8.34766 8.98032 6.66016 10.2499 5.39062C11.5194 4.12109 13.2069 3.42188 14.9999 3.42188C16.7928 3.42188 18.4803 4.12109 19.7499 5.39062C21.0194 6.66016 21.7186 8.34766 21.7186 10.1406C21.7186 11.9336 21.0194 13.6211 19.7499 14.8906C18.4803 16.1602 16.7928 16.8594 14.9999 16.8594Z"
                                fill="#E8392E"></path>
                        </svg>
                        <p></p>
                        <b>
                            Đối tượng
                        </b>
                        <p></p>
                        Độ tuổi: 8-10. Lần đầu tiếp xúc với lập trình
                    </div>

                    <div class="col-6 col-lg-3 text-center px-1 pt-4">
                        <svg width="32" height="32" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M16.9834 0.333374C7.78337 0.333374 0.333374 7.80004 0.333374 17C0.333374 26.2 7.78337 33.6667 16.9834 33.6667C26.2 33.6667 33.6667 26.2 33.6667 17C33.6667 7.80004 26.2 0.333374 16.9834 0.333374ZM17 30.3334C9.63337 30.3334 3.66671 24.3667 3.66671 17C3.66671 9.63337 9.63337 3.66671 17 3.66671C24.3667 3.66671 30.3334 9.63337 30.3334 17C30.3334 24.3667 24.3667 30.3334 17 30.3334Z"
                                fill="#E8392E"></path>
                            <path
                                d="M17.8334 8.66669H15.3334V18.6667L24.0834 23.9167L25.3334 21.8667L17.8334 17.4167V8.66669Z"
                                fill="#E8392E"></path>
                        </svg>
                        <p></p>
                        <b>Thời gian học</b>
                        <p></p>
                        2 tiếng/buổi
                    </div>

                    <div class="col-6 col-lg-3 text-center px-1 pt-4">
                        <svg width="32" height="32" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M16.9999 20.3333C17.3296 20.3333 17.6518 20.2356 17.9259 20.0524C18.2 19.8693 18.4136 19.609 18.5397 19.3045C18.6659 18.9999 18.6989 18.6648 18.6346 18.3415C18.5703 18.0182 18.4115 17.7212 18.1784 17.4881C17.9453 17.255 17.6484 17.0963 17.3251 17.032C17.0018 16.9677 16.6667 17.0007 16.3621 17.1268C16.0576 17.253 15.7973 17.4666 15.6141 17.7407C15.431 18.0148 15.3333 18.337 15.3333 18.6666C15.3333 19.1087 15.5088 19.5326 15.8214 19.8452C16.134 20.1577 16.5579 20.3333 16.9999 20.3333ZM25.3333 20.3333C25.6629 20.3333 25.9851 20.2356 26.2592 20.0524C26.5333 19.8693 26.7469 19.609 26.8731 19.3045C26.9992 18.9999 27.0322 18.6648 26.9679 18.3415C26.9036 18.0182 26.7449 17.7212 26.5118 17.4881C26.2787 17.255 25.9817 17.0963 25.6584 17.032C25.3351 16.9677 25 17.0007 24.6954 17.1268C24.3909 17.253 24.1306 17.4666 23.9475 17.7407C23.7643 18.0148 23.6666 18.337 23.6666 18.6666C23.6666 19.1087 23.8422 19.5326 24.1547 19.8452C24.4673 20.1577 24.8912 20.3333 25.3333 20.3333ZM16.9999 27C17.3296 27 17.6518 26.9022 17.9259 26.7191C18.2 26.536 18.4136 26.2757 18.5397 25.9711C18.6659 25.6666 18.6989 25.3315 18.6346 25.0082C18.5703 24.6849 18.4115 24.3879 18.1784 24.1548C17.9453 23.9217 17.6484 23.763 17.3251 23.6987C17.0018 23.6344 16.6667 23.6674 16.3621 23.7935C16.0576 23.9197 15.7973 24.1333 15.6141 24.4074C15.431 24.6814 15.3333 25.0037 15.3333 25.3333C15.3333 25.7753 15.5088 26.1993 15.8214 26.5118C16.134 26.8244 16.5579 27 16.9999 27ZM25.3333 27C25.6629 27 25.9851 26.9022 26.2592 26.7191C26.5333 26.536 26.7469 26.2757 26.8731 25.9711C26.9992 25.6666 27.0322 25.3315 26.9679 25.0082C26.9036 24.6849 26.7449 24.3879 26.5118 24.1548C26.2787 23.9217 25.9817 23.763 25.6584 23.6987C25.3351 23.6344 25 23.6674 24.6954 23.7935C24.3909 23.9197 24.1306 24.1333 23.9475 24.4074C23.7643 24.6814 23.6666 25.0037 23.6666 25.3333C23.6666 25.7753 23.8422 26.1993 24.1547 26.5118C24.4673 26.8244 24.8912 27 25.3333 27ZM8.66659 20.3333C8.99622 20.3333 9.31845 20.2356 9.59254 20.0524C9.86662 19.8693 10.0802 19.609 10.2064 19.3045C10.3325 18.9999 10.3655 18.6648 10.3012 18.3415C10.2369 18.0182 10.0782 17.7212 9.8451 17.4881C9.61201 17.255 9.31504 17.0963 8.99174 17.032C8.66843 16.9677 8.33332 17.0007 8.02878 17.1268C7.72424 17.253 7.46394 17.4666 7.2808 17.7407C7.09767 18.0148 6.99992 18.337 6.99992 18.6666C6.99992 19.1087 7.17551 19.5326 7.48807 19.8452C7.80064 20.1577 8.22456 20.3333 8.66659 20.3333ZM28.6666 3.66665H26.9999V1.99998C26.9999 1.55795 26.8243 1.13403 26.5118 0.821468C26.1992 0.508908 25.7753 0.333313 25.3333 0.333313C24.8912 0.333313 24.4673 0.508908 24.1547 0.821468C23.8422 1.13403 23.6666 1.55795 23.6666 1.99998V3.66665H10.3333V1.99998C10.3333 1.55795 10.1577 1.13403 9.8451 0.821468C9.53254 0.508908 9.10861 0.333313 8.66659 0.333313C8.22456 0.333313 7.80064 0.508908 7.48807 0.821468C7.17551 1.13403 6.99992 1.55795 6.99992 1.99998V3.66665H5.33325C4.00717 3.66665 2.7354 4.19343 1.79772 5.13111C0.860036 6.06879 0.333252 7.34056 0.333252 8.66665V28.6666C0.333252 29.9927 0.860036 31.2645 1.79772 32.2022C2.7354 33.1399 4.00717 33.6666 5.33325 33.6666H28.6666C29.9927 33.6666 31.2644 33.1399 32.2021 32.2022C33.1398 31.2645 33.6666 29.9927 33.6666 28.6666V8.66665C33.6666 7.34056 33.1398 6.06879 32.2021 5.13111C31.2644 4.19343 29.9927 3.66665 28.6666 3.66665ZM30.3333 28.6666C30.3333 29.1087 30.1577 29.5326 29.8451 29.8452C29.5325 30.1577 29.1086 30.3333 28.6666 30.3333H5.33325C4.89122 30.3333 4.4673 30.1577 4.15474 29.8452C3.84218 29.5326 3.66659 29.1087 3.66659 28.6666V13.6666H30.3333V28.6666ZM30.3333 10.3333H3.66659V8.66665C3.66659 8.22462 3.84218 7.8007 4.15474 7.48814C4.4673 7.17557 4.89122 6.99998 5.33325 6.99998H28.6666C29.1086 6.99998 29.5325 7.17557 29.8451 7.48814C30.1577 7.8007 30.3333 8.22462 30.3333 8.66665V10.3333ZM8.66659 27C8.99622 27 9.31845 26.9022 9.59254 26.7191C9.86662 26.536 10.0802 26.2757 10.2064 25.9711C10.3325 25.6666 10.3655 25.3315 10.3012 25.0082C10.2369 24.6849 10.0782 24.3879 9.8451 24.1548C9.61201 23.9217 9.31504 23.763 8.99174 23.6987C8.66843 23.6344 8.33332 23.6674 8.02878 23.7935C7.72424 23.9197 7.46394 24.1333 7.2808 24.4074C7.09767 24.6814 6.99992 25.0037 6.99992 25.3333C6.99992 25.7753 7.17551 26.1993 7.48807 26.5118C7.80064 26.8244 8.22456 27 8.66659 27Z"
                                fill="#E8392E"></path>
                        </svg>
                        <p></p>
                        <b>Số buổi</b>
                        <p></p>
                        42 buổi học
                    </div>
                    <div class="col-6 col-lg-3 text-center px-1 pt-4">
                        <svg width="36" height="36" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M20 9.72504L19.6088 9.84379L3.98377 15.0788L0.431274 16.25L2.50002 16.9125V27.85C1.75377 28.2838 1.25002 29.075 1.25002 30C1.25002 30.6631 1.51342 31.299 1.98226 31.7678C2.4511 32.2366 3.08698 32.5 3.75002 32.5C4.41307 32.5 5.04895 32.2366 5.51779 31.7678C5.98663 31.299 6.25002 30.6631 6.25002 30C6.25002 29.075 5.74627 28.2838 5.00002 27.85V17.775L7.50002 18.5938V25C7.50002 26.025 8.12502 26.875 8.86752 27.4625C9.61002 28.0463 10.5325 28.4588 11.64 28.8288C13.8575 29.5663 16.78 30 20 30C23.22 30 26.1425 29.5675 28.36 28.8275C29.4675 28.4588 30.39 28.0463 31.1325 27.4613C31.875 26.875 32.5 26.025 32.5 25V18.5938L36.0163 17.4213L39.5688 16.25L36.015 15.0775L20.39 9.84379L20 9.72504ZM20 12.3438L31.7188 16.25L20 20.1563L8.28127 16.25L20 12.3438ZM10 19.4538L19.61 22.6563L20 22.7738L20.3913 22.655L30 19.4525V25C30 25.0125 30.005 25.1575 29.6088 25.4688C29.2138 25.7813 28.505 26.175 27.5775 26.485C25.725 27.1013 22.9938 27.5 20 27.5C17.0063 27.5 14.275 27.1025 12.4213 26.4838C11.4963 26.175 10.7863 25.78 10.3913 25.4688C9.99377 25.1563 10 25.0125 10 25V19.4525V19.4538Z"
                                fill="#E8392E"></path>
                        </svg>
                        <p></p>
                        <b>Số học viên</b>
                        <p></p>
                        8 - 10 bạn
                    </div>
                </div>
            </div>
        </div>


        <div class="accordion1 px-3 py-3">

            <div class="accordion-item active">
                <h3>
                    Mục tiêu:
                </h3>
                <p>

                        <em>
                            Những gì kết quả tốt đẹp thể hiện ra bên ngoài, nhìn thấy được xuất phát từ những gì chúng
                            ta không nhìn thấy. Chính thuật toán, những gì thuộc về tư duy không nhìn thấy, là cốt lõi
                            để các em có thể lập ra những trương trình thông minh sinh động, thu hút.

                            <br>
                            Lớp rèn luyện thuật toán & lập trình sẽ là quá trình liên tục học tập,
                            với các bài toán theo suốt quá trình học của các em từ tiểu học.
                            Chính vì vậy lớp thuật toán sẽ không chỉ về lập trình mà còn giúp các em củng cố tư duy toán
                            cho các kỳ thi toán cũng như tin học.

                        </em>

                </p>

                <div class="row">
                    <div class="col-sm-7">

                        <div class="title" data-code-pos='ppp16973639566751'>
                            <div class="title-text">
                                <h3>Danh sách bài toán tham khảo</h3>
                            </div>
                        </div>
                        <div class="description" style="">
                            <div>

                                <?php

                                $str = "Nhập số n từ bàn phím và tính tổng S = 1/1 + 1/2 + ... + 1/n.
Nhập số n từ bàn phím và tính tổng S = 1/(1.2) + 1/(2.3) + ... + 1/(n.(n+1)).
Tính tổng các số của dãy sau: 1 + 5 + 9 + 13 + ..... + 205.
Nhập số n từ bàn phím, in ra các số nguyên tố < n.
Nhập số n từ bàn phím, in ra khai triển của số n thành tích các thừa số nguyên tố. Ví dụ 20 = 2.2.5.
Nhập số n, in ra n số hạng đầu tiên của dãy số Fibonacci.
Nhập 2 số tự nhiên n, m, tính ước số chung lớn nhất của n, m.
Nhập 3 số tự nhiên n, m, p tính ước số chung lớn nhất của n, m, p.
Nhập 2 số tự nhiên n, m, tính bội số chung nhỏ nhất của n, m.
Nhập 3 số tự nhiên n, m, p tính bội số chung nhỏ nhất của n, m, p.
Nhập số n và dãy số a1, a2, ...., an từ bàn phím. Tính:
a) Tổng các số của dãy trên.
b) Tìm phần tử (chỉ số) tương ứng với số lớn nhất và nhỏ nhất của dãy trên.
Nhập số n và dãy số a1, a2, ...., an từ bàn phím. Tìm một dãy em liên tục cực đại đơn điệu tăng của dãy số trên.
Tương tự bài 12. Tìm một dãy em liên tục cực đại các số > 0 của dãy trên.
Nhập số n từ bàn phím, tìm và in ra tất cả các ước số nguyên tố của số n.
Nhập 3 số m, n, p từ bàn phím. tìm và in ra tất cả các ước số chung của 3 số trên.
Số hoàn hảo là số = tổng các ước số thực sự của số đó. Tìm tất cả các số hoàn hảo < 1000000000.
Nhập số n từ bàn phím, tìm:
a) số nguyên tố nhỏ nhất > n.
b) số nguyên tố lớn nhất < n.
Nhập từ bàn phím số n, hãy viết n trong hệ nhị phân.
Nhập từ bàn phím số n, hãy viết n trong hệ hex (hệ đếm 16).
Nhập số n và dãy số a1, a2, ...., an từ bàn phím. Tìm ra các phần tử cực trị địa phương của dãy này (phần tử cực trị địa phương nếu nó cùng lớn hơn hoặc nhỏ hơn 2 số bên cạnh).
Bài toán tính tổng các số từ 1 đến N , từ M đến N
Bài toán sắp xếp các số theo phương pháp đổi chỗ, nổi bọt
Bài toán tìm đường đi của em kiến từ A đến B
";

                                $mm = explode("\n", $str);
                                echo "\n <ul>";
                                foreach ($mm AS $line) {
                                    $line = trim($line);
                                    if (!$line)
                                        continue;
                                    echo " <li>$line</li> ";

                                }
                                echo "\n </ul>";
                                ?>


                            </div>
                            <div></div>
                        </div>
                    </div>
                    <div class="col-md-5 txt-center pt-3" data-code-pos='ppp16973640395041'>
                        <h3>Học Phí </h3>

                        <div
                            style="background-color: midnightblue; color: white; padding: 10px 20px; width: 180px; margin: 10px auto; font-size: larger">
                            2.000.000 VNĐ
                        </div>
                        (20 buổi học, 2h/1 buổi)
                    </div>
                </div>

            </div>

        </div>
    </div>



    <div class="container  txt-center" data-code-pos='ppp16857567667949'>

        <img src="/slink/30/000/002/3301020b00" style="width: 100%; max-width: 900px" alt="">
    </div>



    <div class="container-fluid p-4 mt-3  bg-light" data-code-pos='ppp16857567667949'
         style="">

        <div class="container">
            <div style="max-width: 900px; margin: 0 auto ; text-align: center">
                <h3 class="p-2 mb-4">
                    Giảng viên chính
                </h3>
                <div class="row">

                    <div class="col-sm-12 col-md-12 txt-left">
                        <div class="row">
                            <div class="col-lg-2 col-sm-0">
                            </div>
                            <div class="col-lg-4 mt-2" style="text-align: center">
                                <img src="/images/lad1.png" alt="" style="width: 100%; max-width: 150px">
                            </div>
                            <div class="col-lg-4 mt-2" style="font-style: italic; font-size: 90%">
                                <span style="color: brown">
                        - Thầy
                                <b>
                                Lê Anh Dũng</b>, tốt nghiệp Khoa Toán Tin Đại học Bách Khoa Hà nội.
                                    </span>
                        <br>- Thầy có nhiều năm công tác trong ngành Công nghệ thông tin và Đào tạo với nhiều dự án trong và ngoài nước.
                                <br>
                                -
                        Với niềm đam mê công nghệ và tâm huyết trong lĩnh vực đào tạo,
                        thầy mong muốn truyền cảm hứng và kiến thức về khoa học công nghệ cho thế hệ trẻ,
                        để góp sức vào sự phát triển mạnh mẽ của ngành công nghệ Việt Nam.
                            </div>
                            <div class="col-lg-2 col-sm-0">
                            </div>
                        </div>
                    </div>

                    {{--                <div class="col-sm-12 col-md-6 txt-center pt-3">--}}

                    {{--                    <h3>Học Phí </h3>--}}

                    {{--                    <div style="background-color: midnightblue; color: white; padding: 10px 20px; width: 180px; margin: 10px auto; font-size: larger">--}}
                    {{--                        1.100.000 VNĐ--}}
                    {{--                    </div>--}}
                    {{--                    (14 buổi học + 14 buổi review online)--}}
                    {{--                </div>--}}

                </div>

            </div>
        </div>
    </div>

    <div class="container pt-4 mt-3 bg-light qqqq1111" data-code-pos='ppp16894976591' style="display: none">

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

    <div class="container pt-2 qqqq1111" data-code-pos='ppp16897795008271' style="display: none">
        <?php

        $ui = BlockUi::showEditButtonStatic('doi-tac');

        ?>

        <div class="product_partner" >
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

