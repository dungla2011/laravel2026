@extends(getLayoutNameMultiReturnDefaultIfNull())

@section("title")
    MyTree - Phần mềm vẽ gia phả online, gia phả số, phả đồ online, tiện lợi nhất
@endsection

@section("og_title")
    MyTree - Phần mềm vẽ gia phả online, gia phả số, phả đồ online, tiện lợi nhất
@endsection

@section("content")

    <style>
        .img_list {
            /*border-bottom: 1px dashed #ccc;*/
            min-height: 100px;
            text-align: center;
            padding: 30px 5px;
            background-color: #eeefff;
        }

        .img_list .img_title {
            font-style: italic;
            margin-top: 20px
        }

        .img_list img {
            max-width: 100%;
            height: auto;
            border: 0px dashed #ccc;
            /*padding: 10px;*/
        }

        .container_youtube {
            margin-bottom: 20px;
            position: relative;
            width: 100%;
            height: 0;
            padding-bottom: 56.25%;
        }

        .video_youtube_iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

    </style>

    <!-- FileView=/application/module/default/index-view/glx2021/index.phtml -->
    <div class="hero-slider-wrapper">
        <div class="hero-slider owl-carousel dots-over" data-nav="true" data-dots="true" data-autoplay="true">
            <div class="owl-slide d-flex align-items-center bg-overlay bg-overlay-400"
                 style="background-image: url('/images/mytree/slide-01.png');">
                <div class="container">
                    <div class="row">
                        <div
                            class="col-md-10 offset-md-1 col-lg-7 offset-lg-0 col-xl-6 col-xxl-5 text-center text-lg-start">
                            <h2 class="display-1 fs-56 mb-4 text-white animated-caption"
                                data-anim="animate__slideInDown" data-anim-delay="500">
                                Tạo Gia phả - phả hệ, Sơ đồ tổ chức
                            </h2>
                            <p class="lead fs-23 lh-sm mb-7 text-white animated-caption"
                               data-anim="animate__slideInRight" data-anim-delay="1000">MyTree mang đến giải pháp nhanh
                                chóng tiện lợi nhất cho bạn!</p>
                            <div class="animated-caption" data-anim="animate__slideInUp" data-anim-delay="500"><a
                                    href="/my-tree" class="btn btn-lg btn-outline-white rounded-pill">Bắt đầu</a></div>
                        </div>
                        <!--/column -->
                    </div>
                    <!--/.row -->
                </div>
                <!--/.container -->
            </div>
            <!--/.owl-slide -->
            <div class="owl-slide d-flex align-items-center bg-overlay bg-overlay-400"
                 style="background-image: url(/images/mytree/slide-02.png);">
                <div class="container light-gallery-wrapper">
                    <div class="row">
                        <div class="col-md-11 col-lg-8 col-xl-7 col-xxl-6 mx-auto text-center">
                            <h2 class="display-1 fs-56 mb-4 text-white animated-caption"
                                data-anim="animate__slideInDown" data-anim-delay="500">MyTree được sự tin tưởng của hàng
                                triệu người dùng</h2>

                            {{--                            <p class="lead fs-23 lh-sm mb-7 text-white animated-caption"--}}
                            {{--                               data-anim="animate__slideInRight" data-anim-delay="1000">Bởi sản phẩm và hỗ trợ</p>--}}
                            <div class="animated-caption" data-anim="animate__slideInUp" data-anim-delay="500"><a
                                    href="/my-tree" class="btn btn-lg btn-outline-white rounded-pill">Bắt đầu</a></div>

                        </div>
                        <!--/column -->
                    </div>
                    <!--/.row -->
                </div>
                <!--/.container -->
            </div>
            <!--/.owl-slide -->
            <div class="owl-slide d-flex align-items-center bg-overlay bg-overlay-400"
                 style="background-image: url(/images/mytree/slide-03.png);">
                <div class="container">
                    <div class="row">
                        <div
                            class="col-md-10 offset-md-1 col-lg-7 offset-lg-5 col-xl-6 offset-xl-6 col-xxl-5 offset-xxl-6 text-center text-lg-start">
                            <h2 class="display-1 fs-56 mb-4 text-white animated-caption"
                                data-anim="animate__slideInDown" data-anim-delay="500"></h2>
                            <p class="lead fs-23 lh-sm mb-7 text-white animated-caption"
                               data-anim="animate__slideInRight" data-anim-delay="1000">MyTree luôn lắng nghe, cải tiến
                                dịch vụ đáp ứng yêu cầu đa dạng của người dùng</p>
                            <div class="animated-caption" data-anim="animate__slideInUp" data-anim-delay="500">
                                <a
                                    href="/my-tree" class="btn btn-lg btn-outline-white rounded-pill">Bắt đầu</a></div>
                        </div>
                        <!--/column -->
                    </div>
                    <!--/.row -->
                </div>
                <!--/.container -->
            </div>
            <!--/.owl-slide -->
        </div>
        <!--/.hero-slider -->
    </div>
    <!--/.hero-slider-wrapper -->


    <section id="intro-glx" data-code-pos='qqq1634973176496' class="wrapper bg-light">


        <br><br>
        <div class="container py-md-1" style="text-align: center">
            <a target="_blank"
               href="https://www.youtube.com/watch?v=y8aUcqle-y8&list=PL2ytCDlW-wDcV2gx1UqabbEFMb19R0viY&index=11&t=6s">
                <img src="/images/icon/apple_store.png" alt="" style="width: 160px; margin-right: 10px; margin-bottom: 5px">

                <img src="/images/icon/android_store.png" alt="" style="width: 160px; margin-right: 10px;">
            </a>
        </div>

        <div class="container py-1 py-md-6">

                <!--/column -->


                    <!--                <p class="mb-6"> Bao gồm các giải pháp Phần mềm, Phần cứng, Tích hợp hệ thống, Bảo mật thông tin</p>-->
                    <div class="row gy-3 gx-xl-8">
                        <div class="col-lg-2">
                        </div>
                        <div class="col-lg-8">
                            <img src="/template/glx2021/assets/img/icons/megaphone.svg"
                                 class="svg-inject icon-svg icon-svg-md mb-4" alt=""/>
                            <h2 class="display-4 mb-3" style="display: inline-block; margin-left: 20px">Giới thiệu MyTree</h2>

                            <ul class="icon-list bullet-bg bullet-soft-primary mb-0">
                                <li>
                                    <span><i class="uil uil-check"></i></span><span>
                                    MyTree gồm Phần mềm Online <b style="color: deepskyblue">Miễn phí</b> vẽ sơ đồ Cây gia phả, Cây phả hệ, Sơ đồ tổ chức, Cây lịch sử
                                </span>
                                </li>

                                <li class="mt-3"><span><i class="uil uil-check"></i></span><span>
                                        MyTree Cho phép Lưu trữ, chia sẻ, tải xuống, in ấn sơ đồ
                                </span>
                                </li>
                                <li><span><i class="uil uil-check"></i></span><span>
                                    MyTree có gói
                                        <span style="color: red">
                                        Thiết kế Web riêng
                                            </span>
                                        cho từng dòng họ, gia đình, tổ chức, doanh nghiệp
                                        <a href="https://docs.google.com/document/d/1gVl9jVy9GwwJY-UhcJP9_PA9jmN4e2M7NNFrd7XanSo/edit">
                                        Xem thêm tại đây
                                            </a>
                                </span>
                                </li>
                                <li><span><i class="uil uil-check"></i></span><span>
                                    Cung cấp miễn phí cho các tổ chức hành chính sự nghiệp, trường học, cây lịch sử dân tộc
                                </span>
                                </li>
                                <li>
                                    <i class="uil uil-check"></i>
                                    MyTree đã trải qua 03 phiên bản, được xây dựng phiên bản 1.0 từ 2015 với mục đích cá nhân, và phiên bản hiện tại là 3.0 do nhu cầu lớn từ cộng đồng
                                </li>
                                <li>
                                    <i class="uil uil-check"></i>
                                    MyTree đặt tên với mục đích xây dựng Cây không chỉ riêng về cây gia phả gia đình
                                </li>
                            </ul>
                        </div>

                        <div class="col-lg-2">
                        </div>
                        <!--/column -->
                    </div>
                    <!--/.row -->

                <!--/column -->


        <!-- /.container -->
    </section>




    <!-- /section -->

    <section id="guide-glx" class="wrapper bg-gradient-primary" style="text-align: center; padding: 40px 10px">

        <h2 class="display-1 mb-4" data-cue="zoomIn" data-interval="-200" style="font-size: 30px">
            VIDEO HƯỚNG DẪN SỬ DỤNG
        </h2>
        <div style="max-width: 1000px; margin: 0px auto;">

            {{--            <div class="container_youtube" style="">--}}
            {{--                <iframe class="video_youtube_iframe" width="100%" src="https://www.youtube.com/embed/au0UI6yunCA" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>--}}
            {{--        </div>--}}


            <a href="https://www.youtube.com/watch?v=JatnYPLi_pU&list=PL2ytCDlW-wDcV2gx1UqabbEFMb19R0viY&index=2">
                <img src="/images/mytree/guide1.png" style="width: 100%; max-width: 1200px"
                     alt="Hướng dẫn sử dụng phần mềm tạo cây gia phả, phả đồ MyTree.vn">
            </a>

            <p></p>

            <a href="https://www.youtube.com/watch?v=JatnYPLi_pU&list=PL2ytCDlW-wDcV2gx1UqabbEFMb19R0viY&index=2"> <i
                    class="fa fa-hand-grab-o"></i>Xem các Video Hướng dẫn Toàn tập sử dụng MyTree</a>
        </div>
    </section>


    <!-- /section -->
    <section class="wrapper bg-soft-primary" style="display: none">
        <div class="container py-10 py-md-12 text-center">
            <div class="row">
                <div class="col-md-9 col-lg-7 col-xl-7 mx-auto text-center">
                    <img src="/template/glx2021/assets/img/icons/puzzle-2.svg"
                         class="svg-inject icon-svg icon-svg-md mb-4" alt=""/>
                    <h2 class="display-4 mb-3">Join Our Community</h2>
                    <p class="lead fs-lg mb-6 px-xl-10 px-xxl-15">We are trusted by over 5000+ clients. Join them by
                        using our services and grow your business.</p>
                </div>
                <!-- /column -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-md-6 col-lg-5 col-xl-4 mx-auto">
                    <div class="newsletter-wrapper">
                        <!-- Begin Mailchimp Signup Form -->
                        <div id="mc_embed_signup2">
                            <form
                                action="https://elemisfreebies.us20.list-manage.com/subscribe/post?u=aa4947f70a475ce162057838d&amp;id=b49ef47a9a"
                                method="post" id="mc-embedded-subscribe-form2" name="mc-embedded-subscribe-form"
                                class="validate" target="_blank" novalidate>
                                <div id="mc_embed_signup_scroll2">
                                    <div class="mc-field-group input-group form-floating">
                                        <input type="email" value="" name="EMAIL" class="required email form-control"
                                               placeholder="Email Address" id="mce-EMAIL2">
                                        <label for="mce-EMAIL2">Email Address</label>
                                        <input type="submit" value="Join" name="subscribe" id="mc-embedded-subscribe2"
                                               class="btn btn-primary">
                                    </div>
                                    <div id="mce-responses2" class="clear">
                                        <div class="response" id="mce-error-response2" style="display:none"></div>
                                        <div class="response" id="mce-success-response2" style="display:none"></div>
                                    </div>
                                    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                                    <div style="position: absolute; left: -5000px;" aria-hidden="true"><input
                                            type="text" name="b_ddc180777a163e0f9f66ee014_4b1bcfa0bc" tabindex="-1"
                                            value=""></div>
                                    <div class="clear"></div>
                                </div>
                            </form>
                        </div>
                        <!--End mc_embed_signup-->
                    </div>
                    <!-- /.newsletter-wrapper -->
                </div>
                <!-- /column -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container -->
    </section>
    <!-- /section -->



    <section class="wrapper bg-gradient-primary">
        <div class="container py-5 pt-md-5 pb-md-5">
            <div class="row text-center">
                <div class="col-lg-9 col-xxl-7 mx-auto" data-cues="zoomIn" data-group="welcome" data-interval="-200"
                     data-disabled="true">
                    <h2 class="display-1 mb-0" data-cue="zoomIn" data-interval="-200" data-group="welcome"
                        data-show="true"
                        style="animation-name: zoomIn; animation-duration: 700ms; animation-timing-function: ease; animation-delay: 0ms;
                        animation-direction: normal; animation-fill-mode: both;">
                        Các tính năng chính
                    </h2>


                </div>
                <!-- /column -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container -->
    </section>

    <section class="wrapper bg-light mb-8" style="">
        <div class="container">

            <div class="" style="max-width: 800px; margin: 0 auto">
                <ul class="icon-list bullet-bg bullet-soft-primary mb-0">
                    <li><span><i class="uil uil-check"></i></span>
                        <span>
                            Thêm thành viên, thêm con cái, vợ chồng, bố mẹ, thêm vợ (chồng) cả, vợ 2, vợ 3...
                        </span>
                    </li>

                    <li><span><i class="uil uil-check"></i></span>
                        <span>
                            Thêm ảnh cho thành viên, danh hiệu chức vụ, ngày sinh, nơi ở, điện thoại, email...
                        </span>
                    </li>
                    <li><span><i class="uil uil-check"></i></span>
                        <span>
                            Soạn tiểu sửa, xem, xuất bản tiểu sử
                    </span>
                    </li>
                    <li><span><i class="uil uil-check"></i></span>
                        <span>
                            Đổi vị trí các thành viên, con cả, con thứ, số thứ tự cao sẽ đứng lên đầu tiên...
                    </span>
                    </li>

                    <li><span><i class="uil uil-check"></i></span>
                        <span>
                            Xóa và phục hồi thành viên, Di chuyển thành viên đến các nhánh...
                    </span>
                    </li>

                    <li><span><i class="uil uil-check"></i></span>
                        <span>
                            Tải và in ấn, cỡ A0-A4, độ phân giải cao (đã kiểm tra in các mẫu dài hơn 10 mét, chiều cao A0)
                   </span>
                    </li>
                    <li><span><i class="uil uil-check"></i></span>
                        <span>
                            Banner cuốn thư linh hoạt chọn hàng chục mẫu đẹp có sẵn, thay đổi style tùy chọn
                    </span>
                    </li>
                    <li><span><i class="uil uil-check"></i></span>
                        <span>
                            Hai chế độ vẽ phả đồ: Dàn trang tự động cân đối phả đồ, Hoặc thay đổi Bằng tay các vị trí mong muốn
                    </span>
                    </li>
                    <li><span><i class="uil uil-check"></i></span>
                        <span>
                            Tải phả đồ dạng Excel để lưu trữ, backup dự phòng
                    </span>
                    </li>

                    <li><span><i class="uil uil-check"></i></span>
                        <span>
                            Chia sẻ phả đồ dễ dàng bằng cách copy địa chỉ web gửi cho mọi người
                    </span>
                    </li>

                    <li><span><i class="uil uil-check"></i></span>
                        <span>
                            Bảo mật: chỉ người tạo phả đồ có thể sửa, mọi người khác chỉ có thể xem
                    </span>
                    </li>

                    <li><span><i class="uil uil-check"></i></span>
                        <span>
                            App trên điện thoại thao tác dễ dàng
                    </span>
                    </li>
                    <li><span><i class="uil uil-check"></i></span>
                        <span>
                            Hàng nghìn thành viên trên một phả đồ
                        </span>
                    </li>

                    <li><span><i class="uil uil-check"></i></span>
                        <span>
                            Xem sửa, xuất bản riêng từng nhánh của một phả đồ lớn
                        </span>
                    </li>

                    <li><span><i class="uil uil-check"></i></span>
                        <span style="color: darkred">
                            Cho phép nhiều người cùng tham gia tạo một phả đồ, không giới hạn độ rộng và sâu
                            <a target="_blank" href="https://www.youtube.com/watch?v=gBCCBiEEfVE&list=PL2ytCDlW-wDcV2gx1UqabbEFMb19R0viY&index=13"> (hướng dẫn)</a>


                    </span>
                    </li>
                </ul>
                <div style="text-align: center; margin: 20px">
                <a target="_blank" href="https://www.youtube.com/watch?v=JatnYPLi_pU&list=PL2ytCDlW-wDcV2gx1UqabbEFMb19R0viY&index=2">
                    <h3 style="color: dodgerblue">
                        <i class="uil uil-check"></i>
                        Xem Hướng dẫn Video tại đây
                    </h3>
                </a>
                </div>
            </div>

        </div>
    </section>

    <section class="wrapper bg-light">
        <div class="container">
            <div class="row gx-lg-0 gy-10 mb-15 mb-md-18 align-items-center">


                <?php

                $mm = [
                    23 => 'Tạo cây dễ dàng, bố trí đẹp mắt',
                    24 => 'Chế bản In ấn linh hoạt, cỡ A0, A1, A2, A3, A4, độ phân giải cao',
                    10 => 'Tạo một Cây mới với tên thành viên đầu tiên',
                    3 => 'Tạo thành viên mới, Menu thành viên đầy đủ tính năng',
                    6 => 'Chọn khung ảnh cho thành viên Nam, Nữ',
                    4 => 'Thay đổi thông tin Banner (Hoành phi, Cuốn thư)',
                    5 => 'Chọn mẫu Banner (Hoành phi, cuốn thư)',
                    22 => 'Mẫu Banner đa dạng',
                    21 => 'Ảnh Banner riêng, tùy chọn gia đình',
                    8 => 'Lựa chọn hiển thị: Huyết thống, Nam-Đinh dòng họ',
                    18 => 'Soạn thảo tiểu sử, xuất bản tiểu sử (thông tin mang tính minh họa)',
                    15 => 'Xem danh sách con, anh em,.. để sửa hàng loạt',
                    11 => 'Xuất bản dạng Excel toàn bộ cây, hoặc nhánh cây',
                    7 => 'Tải xuống dạng ảnh để có thể IN, kích thước, độ nét phân giải không giới hạn (ảnh png, vector)',
                    12 => 'Phục hồi các Thành viên đã Xóa',
                    19 => '',
                    20 => 'Tương thích tốt với mọi thiết bị phổ biến: Iphone, Ipad, Android, MacOS, Window...',

                ];

                $cc = 0;
                foreach ($mm AS $i=>$title){
                if (!$title)
                    continue;
                $cc++;
                $nn = sprintf("%03d", $i);


                $padStyle = "";
                if($cc> 2){

                    $padStyle = ";display: none;";
                }


                if(file_exists(public_path("/images/mytree/$nn.png"))){
                ?>
                <div class="col-sm-12 img_list" style="<?php echo $padStyle ?>">
                    <?php
                    if($cc == 1){
                        echo " <h1> Một số hình ảnh </h1>  <br>";
                    }
                    ?>
                    <img title="<?php echo $nn ?>" src="/images/mytree/<?php echo $nn ?>.png" alt="">
                    <div class="img_title" style="">
                        <?php
                        echo $cc . " . " . $title;
                        ?>
                    </div>
                    <?php
                    if($cc == 2){
                        echo " <button id='view_more_image' class='btn btn-sm btn-primary mt-3'> Xem thêm </button> ";
                    }
                    ?>
                </div>
                <?php
                }
                }
                ?>

                <div class="col-sm-12">
                    {{--                    (Ảnh thành viên chỉ mang tính minh họa)--}}
                </div>
            </div>
            <div class="col-lg-6">


            </div>

        </div>

        <!-- /.row -->

        </div>

    </section>

    <script>

        window.addEventListener('load', function () {
            let lastClickTime = 0;
            document.getElementById('view_more_image').addEventListener('click', function () {
                let all = document.querySelectorAll('.img_list');
                all.forEach(function (item) {
                    item.style.display = 'block';
                });
                this.style.display = 'none';
            });
        });


    </script>

@endsection
