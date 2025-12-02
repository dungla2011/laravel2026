@extends(getLayoutNameMultiReturnDefaultIfNull())

@section("title")
    4Share.vn - Dịch vụ Lưu trữ chia sẻ file hàng đầu
@endsection

@section("og_title")
    4Share.vn - Dịch vụ Lưu trữ chia sẻ file hàng đầu
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
                 style="background-image: url('/images/banner-top-4s1.png');">
                <div class="container">
                    <div class="row">
                        <div
                            class="col-md-10 offset-md-1 col-lg-7 offset-lg-0 col-xl-6 col-xxl-5 text-center text-lg-start">
                            <h2 class="display-1 fs-56 mb-4 text-white animated-caption"
                                data-anim="animate__slideInDown" data-anim-delay="500">
                                Truy cập mọi nơi
                            </h2>
                            <p class="lead fs-23 lh-sm mb-7 text-white animated-caption"
                               data-anim="animate__slideInRight" data-anim-delay="1000">
                                4Share mang đến giải pháp nhanh
                                chóng tiện lợi nhất cho bạn!
                            </p>
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
                 style="background-image: url(/images/banner-top-4s1.png);">
                <div class="container light-gallery-wrapper">
                    <div class="row">
                        <div class="col-md-11 col-lg-8 col-xl-7 col-xxl-6 mx-auto text-center">
                            <h2 class="display-1 fs-56 mb-4 text-white animated-caption"
                                data-anim="animate__slideInDown" data-anim-delay="500">4Share được sự tin tưởng của hàng
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
                 style="background-image: url(/images/banner-top-4s1.png);">
                <div class="container">
                    <div class="row">
                        <div
                            class="col-md-10 offset-md-1 col-lg-7 offset-lg-5 col-xl-6 offset-xl-6 col-xxl-5 offset-xxl-6 text-center text-lg-start">
                            <h2 class="display-1 fs-56 mb-4 text-white animated-caption"
                                data-anim="animate__slideInDown" data-anim-delay="500"></h2>
                            <p class="lead fs-23 lh-sm mb-7 text-white animated-caption"
                               data-anim="animate__slideInRight" data-anim-delay="1000">4Share luôn lắng nghe, cải tiến
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




        <div class="container py-1 py-md-6">

                <!--/column -->


                    <!--                <p class="mb-6"> Bao gồm các giải pháp Phần mềm, Phần cứng, Tích hợp hệ thống, Bảo mật thông tin</p>-->
                    <div class="row gy-3 gx-xl-8">
                        <div class="col-lg-2">
                        </div>
                        <div class="col-lg-8">
                            <img src="/template/glx2021/assets/img/icons/megaphone.svg"
                                 class="svg-inject icon-svg icon-svg-md mb-4" alt=""/>
                            <h2 class="display-4 mb-3" style="display: inline-block; margin-left: 20px">Giới thiệu 4Share</h2>

                            <ul class="icon-list bullet-bg bullet-soft-primary mb-0">
                                <li>
                                    <span><i class="uil uil-check"></i></span><span>
                                    Dịch vụ Lưu trữ chia sẻ file hàng đầu
                                </span>
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


@endsection
