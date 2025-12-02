@extends(getLayoutNameMultiReturnDefaultIfNull())

@section("title")
 Trang Chủ - GiaPha.One - Phần mềm vẽ phả đồ online, gia phả online tiện lợi nhất @endsection

@section("og_title")
GiaPha.One: phần mềm tạo phả đồ online, phả hệ online, gia phả online, sơ đồ tổ chức online
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
                               data-anim="animate__slideInRight" data-anim-delay="1000">GiaPha.One mang đến giải pháp nhanh
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
                                data-anim="animate__slideInDown" data-anim-delay="500">GiaPha.One được sự tin tưởng của hàng
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
                               data-anim="animate__slideInRight" data-anim-delay="1000">GiaPha.One luôn lắng nghe, cải tiến
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
        <div class="container py-10 py-md-12">
            <div class="row gx-lg-8 gx-xl-12 gy-10 mb-14x mb-md-17xx align-items-center">
                <div class="col-lg-6 position-relative order-lg-2">
                    <div class="shape bg-dot primary rellax w-16 h-20" data-rellax-speed="1"
                         style="top: 3rem; left: 5.5rem"></div>
                    <div class="overlap-grid overlap-grid-2">
                        <div class="item">
                            <figure class="rounded shadow"><img src="/template/glx2021/assets/img/photos/about3.jpg"
                                                                srcset="/template/glx2021/assets/img/photos/about3@2x.jpg 2x"
                                                                alt=""></figure>
                        </div>
                        <div class="item">

                            <figure class="rounded shadow"><img
                                    src="https://cdn-glx-8.galaxycloud.vn/tool/media/static.lib?sid=100&db68=1&type=mg&id=vd012977&media=image&w=400"
                                    srcset="https://cdn-glx-8.galaxycloud.vn/tool/media/static.lib?sid=100&db68=1&type=mg&id=vd012977&media=image&w=400"
                                    alt=""></figure>
                        </div>
                    </div>
                </div>

                <!--/column -->
                <div class="col-lg-6">
                    <img src="/template/glx2021/assets/img/icons/megaphone.svg"
                         class="svg-inject icon-svg icon-svg-md mb-4" alt=""/>
                    <h2 class="display-4 mb-3" style="display: inline-block; margin-left: 20px">Giới thiệu GiaPha.One</h2>

                    <!--                <p class="mb-6"> Bao gồm các giải pháp Phần mềm, Phần cứng, Tích hợp hệ thống, Bảo mật thông tin</p>-->
                    <div class="row gy-3 gx-xl-8">
                        <div class="col-xl-6">
                            <ul class="icon-list bullet-bg bullet-soft-primary mb-0">
                                <li><span><i class="uil uil-check"></i></span><span>
                                    GiaPha.One gồm Phần mềm Online web vẽ sơ đồ Cây gia phả, Cây phả hệ, Sơ đồ tổ chức, Cây lịch sử
                                </span>
                                </li>
                                <li class="mt-3"><span><i class="uil uil-check"></i></span><span>
                                        Cho phép Lưu trữ, chia sẻ, tải xuống, in ấn sơ đồ
                                </span>
                                </li>
                            </ul>
                        </div>
                        <!--/column -->
                        <div class="col-xl-6">
                            <ul class="icon-list bullet-bg bullet-soft-primary mb-0">
                                <li><span><i class="uil uil-check"></i></span><span>
                                    GiaPha.One có gói Thiết kế Web riêng cho từng dòng họ, gia đình, tổ chức, doanh nghiệp
                                        <a href="https://docs.google.com/document/d/1gVl9jVy9GwwJY-UhcJP9_PA9jmN4e2M7NNFrd7XanSo/edit">
                                        Xem thêm tại đây
                                            </a>
                                </span>
                                </li>
                                <li><span><i class="uil uil-check"></i></span><span>
                                    Cung cấp miễn phí cho các tổ chức hành chính sự nghiệp, trường học, cây lịch sử dân tộc
                                </span>
                                </li>
                            </ul>
                        </div>
                        <!--/column -->
                    </div>
                    <!--/.row -->
                </div>
                <!--/column -->
            </div>
            <!--/.row -->


        </div>
        <!-- /.container -->
    </section>




    <!-- /section -->

    <section class="wrapper bg-gradient-primary" style="text-align: center; padding: 40px 10px">

        <h2 class="display-1 mb-4" data-cue="zoomIn" data-interval="-200">
            HƯỚNG DẪN SỬ DỤNG
        </h2>
        <div style="max-width: 1000px; margin: 0px auto;">

{{--            <div class="container_youtube" style="">--}}
            {{--                <iframe class="video_youtube_iframe" width="100%" src="https://www.youtube.com/embed/au0UI6yunCA" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>--}}
{{--        </div>--}}


                <a href="https://www.youtube.com/watch?v=JatnYPLi_pU&list=PL2ytCDlW-wDcV2gx1UqabbEFMb19R0viY&index=2">
                <img  src="/images/mytree/guide1.png" style="width: 100%; max-width: 1200px" alt="Hướng dẫn sử dụng phần mềm tạo cây gia phả, phả đồ GiaPha.One">
                </a>

            <p></p>

            <a href="https://www.youtube.com/watch?v=JatnYPLi_pU&list=PL2ytCDlW-wDcV2gx1UqabbEFMb19R0viY&index=2"> <i class="fa fa-hand-grab-o"></i>Xem các Video Hướng dẫn Toàn tập sử dụng GiaPha.One</a>
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
        <div class="container py-5 pt-md-10 pb-md-5">
            <div class="row text-center">
                <div class="col-lg-9 col-xxl-7 mx-auto" data-cues="zoomIn" data-group="welcome" data-interval="-200"
                     data-disabled="true">
                    <h2 class="display-1 mb-4" data-cue="zoomIn" data-interval="-200" data-group="welcome"
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

    <section class="wrapper bg-light">
        <div class="container">
            <div class="row gx-lg-0 gy-10 mb-15 mb-md-18 align-items-center">


                <?php

                $mm = [
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

                if(file_exists(public_path("/images/mytree/$nn.png"))){
                ?>
                <div class="col-sm-12 img_list">
                    <img title="<?php echo $nn ?>" src="/images/mytree/<?php echo $nn ?>.png" alt="">
                    <div class="img_title" style="">
                        <?php
                        echo $cc . " . " . $title;
                        ?>
                    </div>

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


    {{--    <div class="container py-8 py-md-8 white-all-text">--}}
    {{--        <div class="row">--}}
    {{--            <div class="col-xl-10 mx-auto">--}}
    {{--                <div class="row align-items-center counter-wrapper gy-6 text-center">--}}




    {{--                    <div class="col-md-3">--}}
    {{--                        <img src="/template/glx2021/assets/img/icons/check.svg"--}}
    {{--                             class="svg-inject icon-svg icon-svg-lg text-primary mb-3" alt=""/>--}}
    {{--                        <h3 class="counter">8518</h3>--}}
    {{--                        <p>Số người dùng Online</p>--}}
    {{--                    </div>--}}
    {{--                    <!--/column -->--}}
    {{--                    <div class="col-md-3">--}}
    {{--                        <img src="/template/glx2021/assets/img/icons/user.svg"--}}
    {{--                             class="svg-inject icon-svg icon-svg-lg text-primary mb-3" alt=""/>--}}
    {{--                        <h3 class="counter">389200</h3>--}}
    {{--                        <p>Số khách hàng hài lòng</p>--}}
    {{--                    </div>--}}
    {{--                    <!--/column -->--}}
    {{--                    <div class="col-md-3">--}}
    {{--                        <img src="/template/glx2021/assets/img/icons/briefcase-2.svg"--}}
    {{--                             class="svg-inject icon-svg icon-svg-lg text-primary mb-3" alt=""/>--}}
    {{--                        <h3 class="counter">402565</h3>--}}
    {{--                        <p>Số thành viên đăng ký</p>--}}
    {{--                    </div>--}}
    {{--                    <!--/column -->--}}
    {{--                    <div class="col-md-3">--}}
    {{--                        <img src="/template/glx2021/assets/img/icons/award-2.svg"--}}
    {{--                             class="svg-inject icon-svg icon-svg-lg text-primary mb-3" alt=""/>--}}
    {{--                        <h3 class="counter">45098060</h3>--}}
    {{--                        <p>Số bản ghi được tạo</p>--}}
    {{--                    </div>--}}
    {{--                    <!--/column -->--}}
    {{--                </div>--}}
    {{--                <!--/.row -->--}}
    {{--            </div>--}}
    {{--            <!-- /column -->--}}
    {{--        </div>--}}
    {{--        <!-- /.row -->--}}
    {{--    </div>--}}
@endsection
