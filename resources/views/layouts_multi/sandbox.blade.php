<!DOCTYPE html>
<html lang="en">

<head data-code-pos='ppp17370203296921'>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title')
    </title>

    <meta name="keywords" content="Dịch vụ lưu trữ và chia sẻ dữ liệu trực tuyến giúp khách hàng lưu trữ thông tin,         dữ liệu (album ảnh, phim, phần mềm, tài liệu, game, nhạc, v.v...) mọi lúc,         mọi nơi, tương thích trên mọi thiết bị.">

    <meta name="description" content="4Share.vn là dịch vụ lưu trữ và chia sẻ dữ liệu trực tuyến giúp khách hàng lưu trữ thông tin,         dữ liệu (album ảnh, phim, phần mềm, tài liệu, game, nhạc, v.v...) mọi lúc,         mọi nơi, tương thích trên mọi thiết bị." />

    <meta name="author" content="4share.vn">
    <meta name="copyright" content="4share.vn"/>

    <meta property="og:image" content="/images/4s/sharing.jpg">
    <meta property="og:title" content="@yield('title')">
    <meta property="og:description" content="4Share.vn là dịch vụ lưu trữ và chia sẻ dữ liệu trực tuyến giúp khách hàng lưu trữ thông tin,         dữ liệu (album ảnh, phim, phần mềm, tài liệu, game, nhạc, v.v...) mọi lúc,         mọi nơi, tương thích trên mọi thiết bị.">

    <meta name="referrer" content="unsafe-url">

    <link rel="icon" type="image/vnd.microsoft.icon" href="/public/images/4s/icon_4s.ico" />
    <link rel="shortcut icon" type="image/x-icon" href="/public/images/4s/icon_4s.ico" />

    <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed:400,500,700&amp;subset=vietnamese" rel="stylesheet">

    <link href="/assert/css/bootstrap.min.5.1.css" rel="stylesheet"  crossorigin="anonymous">
    <link rel="stylesheet" href="/template/sandbo/assets/css/plugins.css">
    <link rel="stylesheet" href="/template/sandbo/assets/css/style.css">
    <link rel="stylesheet" href="/template/sandbo/assets/css/colors/yellow.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"
          integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" href="/vendor/toastr/toastr.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Birthstone&family=Imperial+Script&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

    {{--    <link rel="preload" href="/template/sandbo/assets/css/fonts/thicccboi.css" as="style" onload="this.rel='stylesheet'">--}}

    <link rel="stylesheet" href="/template/sandbo/custom.css?v=3">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="/admins/jctool.js?v=<?php echo filemtime(public_path().'/admins/jctool.js');?>"></script>

    @yield('js')

    @yield('css')

    <style>
        a.nav-link{
            font-size: 120%;
        }
    </style>

</head>

<?php

\App\Models\BlockUi::showCssHoverBlock();

?>

<body data-code-pos='ppp17370203362141'>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-26965513-1"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-26965513-1');
</script>

<div class="content-wrapper">

    <?php
//    if(isDebugIp())
    if(1)
    {
    ?>

    <style>
        .marquee-container {
            width: 100%;
            overflow: hidden;
            white-space: nowrap;
            position: relative;
            height: 31px;
            background: #fff;
            border: 1px solid #ddd;
        }

        .marquee-text {
            display: inline-block;
            position: absolute;
            animation: marquee 50s linear infinite;
            font-family: 'Birthstone';
            text-align: center;
            font-size: 150%;
            font-weight: bold;
            color: #ffa500;
            width: 100%;
        }

        @keyframes marquee {
            0% { transform: translateX(50%); }  /* Thay đổi từ 100% thành 50% */
            100% { transform: translateX(-100%); }
        }
        @media (max-width: 800px) {
            .marquee-text {
                animation: marquee 15s linear infinite;
            }
        }

        .dl_page {
            font-size:90%; position: absolute; top: 0px; left: 0px; padding: 2px 10px; font-weight: bold; font-style: italic; background-color: white; z-index: 100000; height: 40px;
        }

    </style>
    </head>
    <body>

    <div class="marquee-container" data-code-pos='ppp17382416648041'>
        <div class="dl_page" style="">

            <a target="_blank" href="//4share.download"> <i class="fa fa-download"> </i> Trang download Tổng hợp</a>
        </div>
        <?php

        if($ui = \App\Models\BlockUi::find(10) ?? '')
        if($ui->status)
        {

        ?>
        <div class="marquee-text1" style="color: red; text-align: right">
            <?php
            echo $ui->extra_info;
            ?>
        </div>
        <?php
        }
        ?>
    </div>

    <?php
    }
    ?>

    <header class="wrapper">
        <div class="container-fluid" style="background-color: orange ; padding: 0px">
            <div class="container" style="padding: 0px; position: relative; height: 40px"
                 data-code-pos='ppp17369173094321'>
                <div class="py-1 px-2" style="position: absolute; left: 0px; font-size: 100%; ">
                    <H3 >
                        <a href="/" STYLE="color: white;text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
                        ">
                        4SHARE.VN
                        </a>
                    </H3>
                </div>
                <form action="/search-file" style="position: absolute; right: 0px">
                    <input data-code-pos='ppp17364869693501'
                           placeholder="Tìm hàng triệu file, tài liệu, game, phim..."
                           name="search_string"
                           value="{{request()->get('search_string')}}"
                           class="search-top" style="" type="text"/><button type="submit" class="search-top" style="">
                        <input type='hidden' name='exactly' value='1'/>
                        <input type='hidden' name='sort_by' value='new'/>
                        <i class="uil uil-search"></i>
                    </button>
                </form>
            </div>
        </div>
        <nav class="navbar navbar-expand-lg center-nav transparent navbar-light" data-code-pos='ppp17364868739931'>
            <div class="container flex-lg-row flex-nowrap align-items-center">
                <div class="navbar-brand w-100 py-3" style="">
                    <?php
                    if($email = getCurrentUserEmail())
                    echo "<a class='d-lg-none d-xl-none' href='/member' style=''> <i class='fa fa-user mx-1' title='$email'> </i>" . \LadLib\Common\cstring2::substr_fit_char_unicode($email, 0, 120, 1) . "</a>";
                    ?>
                </div>
                <div class="navbar-collapse offcanvas-nav">

                    <div class="offcanvas-header d-lg-none d-xl-none">
                        <a href="/">
                            4SHARE
                        </a>
                        <button type="button" class="btn-close btn-close-white offcanvas-close offcanvas-nav-close"
                                aria-label="Close"></button>
                    </div>

                    <ul class="navbar-nav qqqq1111">
                        <?php

                        \App\Models\MenuTree::showMenuPublicSandBox();

                        $email0 = \LadLib\Common\cstring2::substr_fit_char_unicode($email, 0, 12, 1);
                        $email0 = mb_ucfirst($email0);
                        ?>

                        <li class="nav-item">
                            <?php
                            if (getCurrentUserId())
                                echo "<a class='nav-link' href='/member'><b> <i class='fa fa-user'></i>  Thành viên </b></a>
                                ";
                            else
                                echo '<a  class="nav-link"  href="/login"><b>Đăng nhập</b></a>';
                            ?>
                        </li>

                    </ul>


                    <!-- /.navbar-nav -->
                </div>
                <!-- /.navbar-collapse -->
                <div class="navbar-other w-100 d-flex ms-auto" style="">
                    <ul class="navbar-nav flex-row align-items-center ms-auto" data-sm-skip="true">
                        <li class="nav-item dropdown language-select text-uppercase" style="display: none">
                            <a class="nav-link dropdown-item dropdown-toggle" href="#" role="button"
                               data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">En</a>
                            <ul class="dropdown-menu">
                                <li class="nav-item"><a class="dropdown-item" href="#">En</a></li>
                                <li class="nav-item"><a class="dropdown-item" href="#">De</a></li>
                                <li class="nav-item"><a class="dropdown-item" href="#">Es</a></li>
                            </ul>
                        </li>

                        <li class="nav-item d-lg-none">
                            <div class="navbar-hamburger">
                                <button class="hamburger animate plain" data-toggle="offcanvas-nav"><span></span>
                                </button>
                            </div>
                        </li>

                    </ul>
                    <!-- /.navbar-nav -->
                </div>
                <!-- /.navbar-other -->
                <div class="offcanvas-info text-inverse">
                    <button type="button" class="btn-close btn-close-white offcanvas-close offcanvas-info-close"
                            aria-label="Close"></button>
                    <a href="/">
                        4SHARE
                    </a>
                    <div class="mt-4 widget">
                        <p>Sandbox is a multipurpose HTML5 template with various layouts which will be a great solution
                            for your business.</p>
                    </div>
                    <!-- /.widget -->
                    <div class="widget">
                        <h4 class="widget-title text-white mb-3">Contact Info</h4>
                        <address> Moonshine St. 14/05 <br/> Light City, London</address>
                        <a href="mailto:first.last@email.com">info@email.com</a><br/> +00 (123) 456 78 90
                    </div>
                    <!-- /.widget -->
                    <div class="widget">
                        <h4 class="widget-title text-white mb-3">Learn More</h4>
                        <ul class="list-unstyled">
                            <li><a href="#">Our Story</a></li>
                            <li><a href="#">Terms of Use</a></li>
                            <li><a href="#">Privacy Policy</a></li>
                            <li><a href="#">Contact Us</a></li>
                        </ul>
                    </div>
                    <!-- /.widget -->
                    <div class="widget">
                        <h4 class="widget-title text-white mb-3">Follow Us</h4>
                        <nav class="nav social social-white" data-code-pos='ppp17373439772821'>
                            <a href="#"><i class="uil uil-twitter"></i></a>
                            <a href="#"><i class="uil uil-facebook-f"></i></a>
                            <a href="#"><i class="uil uil-dribbble"></i></a>
                            <a href="#"><i class="uil uil-instagram"></i></a>
                            <a href="#"><i class="uil uil-youtube"></i></a>
                        </nav>
                        <!-- /.social -->
                    </div>
                    <!-- /.widget -->
                </div>
                <!-- /.offcanvas-info -->
            </div>
            <!-- /.container -->
        </nav>
        <!-- /.navbar -->
    </header>
    <!-- /header -->

    <!-- -->
    <div  style="display: none; z-index: 100000; position: fixed;
     font-size: 80%;
     transform: translateX(-50%);
     bottom: 10px;left: 50%; padding: 3px 6px; text-align: center; color: white; background-color: red">
        <span class="blink12">
        Dịch vụ đang nâng cấp, xin cảm ơn bạn!
            </span>
    </div>
    <!-- /header -->

    @yield('content')

    <!-- /section -->
</div>
<!-- /.content-wrapper -->
<footer class="bg-navy text-inverse">
    <div class="container pt-8 pt-md-8 pb-8 pb-md-8">
        <!--/div -->

        <div class="row gy-6 gy-lg-0">
            <div class="col-md-4 col-lg-3">
                <div class="widget">

                    <p class="mb-4">© 2008 - <?php echo date("Y") ?> 4Share.vn <br class="d-none d-lg-block"/>All rights
                        reserved.</p>
                    <nav class="nav social social-white" data-code-pos='ppp17373439732641'>
                        <a href="#"><i class="uil uil-twitter"></i></a>
                        <a href="#"><i class="uil uil-facebook-f"></i></a>
                        <a href="#"><i class="uil uil-dribbble"></i></a>
                        <a href="#"><i class="uil uil-instagram"></i></a>
                        <a href="#"><i class="uil uil-youtube"></i></a>
                    </nav>
                    <!-- /.social -->
                </div>
                <!-- /.widget -->
            </div>
            <!-- /column -->
            <div class="col-md-4 col-lg-3">
                <div class="widget">
                    <h4 class="widget-title text-white mb-3">Liên hệ</h4>
                    <address class="pe-xl-15 pe-xxl-17">4Share.vn</address>
                    <a href="mailto:#">admin@4share.vn</a><br/> 090.404.3689
                </div>
                <!-- /.widget -->
            </div>
            <!-- /column -->
            <div class="col-md-4 col-lg-3">
                <div class="widget">
                    <h4 class="widget-title text-white mb-3">Thông tin</h4>
                    <ul class="list-unstyled  mb-0">
                        <li><a href="/info/gioi-thieu">Về chúng tôi</a></li>

                        <li><a href="/info/dieu-khoan">Điều khoản</a></li>
                        <li><a href="/info/dieu-khoan">Chính sách Bảo mật</a></li>
                        <li><a href="/info/uploader">Uploader</a></li>
                    </ul>
                </div>
                <!-- /.widget -->
            </div>
            <!-- /column -->
            <div class="col-md-12 col-lg-3">
                <div class="widget">
                    <h4 class="widget-title text-white mb-3">Nhận tin</h4>
                    <p class="mb-5">Đăng ký để nhận tin mới nhất từ 4Share.</p>
                    <div class="newsletter-wrapper">
                        <!-- Begin Mailchimp Signup Form -->
                        <div id="mc_embed_signup2">
                            <form
                                method="post" id="mc-embedded-subscribe-form2" name="mc-embedded-subscribe-form"
                                class="validate dark-fields" novalidate>
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
                <!-- /.widget -->
            </div>
            <!-- /column -->
        </div>
        <!--/.row -->
    </div>
    <!-- /.container -->
</footer>
<div class="progress-wrap">
    <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
        <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"/>
    </svg>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
        integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
        crossorigin="anonymous"></script>
<script src="/template/sandbo/assets/js/plugins.js"></script>
<script src="/template/sandbo/assets/js/theme.js"></script>
<script src="/vendor/toastr/toastr.min.js"></script>

<script src="/admins/toast-show.js"></script>

<link rel="stylesheet" href="/public/css/zalo.css">
<a href="https://zalo.me/0904043689" id="linkzalo" target="_blank" rel="noopener noreferrer"><div id="fcta-zalo-tracking" class="fcta-zalo-mess">
        <span id="fcta-zalo-tracking">Hỗ trợ</span></div><div class="fcta-zalo-vi-tri-nut"><div id="fcta-zalo-tracking" class="fcta-zalo-nen-nut"><div id="fcta-zalo-tracking" class="fcta-zalo-ben-trong-nut"> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 460.1 436.6"><path fill="currentColor" class="st0" d="M82.6 380.9c-1.8-.8-3.1-1.7-1-3.5 1.3-1 2.7-1.9 4.1-2.8 13.1-8.5 25.4-17.8 33.5-31.5 6.8-11.4 5.7-18.1-2.8-26.5C69 269.2 48.2 212.5 58.6 145.5 64.5 107.7 81.8 75 107 46.6c15.2-17.2 33.3-31.1 53.1-42.7 1.2-.7 2.9-.9 3.1-2.7-.4-1-1.1-.7-1.7-.7-33.7 0-67.4-.7-101 .2C28.3 1.7.5 26.6.6 62.3c.2 104.3 0 208.6 0 313 0 32.4 24.7 59.5 57 60.7 27.3 1.1 54.6.2 82 .1 2 .1 4 .2 6 .2H290c36 0 72 .2 108 0 33.4 0 60.5-27 60.5-60.3v-.6-58.5c0-1.4.5-2.9-.4-4.4-1.8.1-2.5 1.6-3.5 2.6-19.4 19.5-42.3 35.2-67.4 46.3-61.5 27.1-124.1 29-187.6 7.2-5.5-2-11.5-2.2-17.2-.8-8.4 2.1-16.7 4.6-25 7.1-24.4 7.6-49.3 11-74.8 6zm72.5-168.5c1.7-2.2 2.6-3.5 3.6-4.8 13.1-16.6 26.2-33.2 39.3-49.9 3.8-4.8 7.6-9.7 10-15.5 2.8-6.6-.2-12.8-7-15.2-3-.9-6.2-1.3-9.4-1.1-17.8-.1-35.7-.1-53.5 0-2.5 0-5 .3-7.4.9-5.6 1.4-9 7.1-7.6 12.8 1 3.8 4 6.8 7.8 7.7 2.4.6 4.9.9 7.4.8 10.8.1 21.7 0 32.5.1 1.2 0 2.7-.8 3.6 1-.9 1.2-1.8 2.4-2.7 3.5-15.5 19.6-30.9 39.3-46.4 58.9-3.8 4.9-5.8 10.3-3 16.3s8.5 7.1 14.3 7.5c4.6.3 9.3.1 14 .1 16.2 0 32.3.1 48.5-.1 8.6-.1 13.2-5.3 12.3-13.3-.7-6.3-5-9.6-13-9.7-14.1-.1-28.2 0-43.3 0zm116-52.6c-12.5-10.9-26.3-11.6-39.8-3.6-16.4 9.6-22.4 25.3-20.4 43.5 1.9 17 9.3 30.9 27.1 36.6 11.1 3.6 21.4 2.3 30.5-5.1 2.4-1.9 3.1-1.5 4.8.6 3.3 4.2 9 5.8 14 3.9 5-1.5 8.3-6.1 8.3-11.3.1-20 .2-40 0-60-.1-8-7.6-13.1-15.4-11.5-4.3.9-6.7 3.8-9.1 6.9zm69.3 37.1c-.4 25 20.3 43.9 46.3 41.3 23.9-2.4 39.4-20.3 38.6-45.6-.8-25-19.4-42.1-44.9-41.3-23.9.7-40.8 19.9-40 45.6zm-8.8-19.9c0-15.7.1-31.3 0-47 0-8-5.1-13-12.7-12.9-7.4.1-12.3 5.1-12.4 12.8-.1 4.7 0 9.3 0 14v79.5c0 6.2 3.8 11.6 8.8 12.9 6.9 1.9 14-2.2 15.8-9.1.3-1.2.5-2.4.4-3.7.2-15.5.1-31 .1-46.5z"></path></svg></div><div id="fcta-zalo-tracking" class="fcta-zalo-text">Chat ngay</div></div></div></a>

<script>
    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) )
    {document.getElementById("linkzalo").href="https://zalo.me/0904043689";}
</script>


</body>

</html>
