<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/images/logo/mytree1_only_logo.png">
    <title>@yield('title')</title>
    <meta name="description" content="Giaphe.one: phần mềm tạo phả hệ online, gia phả online, sơ đồ tổ chức online">
    <meta name="keywords" content="phần mềm tạo phả hệ online, gia phả online, sơ đồ tổ chức online">

    <meta property="og:image" content="/images/logo/mytree1-square.png?v=1">
    <meta property="og:title" content="@yield('og_title')">
    <meta property="og:description" content="Giaphe.one: phần mềm tạo phả hệ online, gia phả online, sơ đồ tổ chức online">

    <link rel="stylesheet" href="/vendor/bootstrap4/bootstrap.min.css">
    <link rel="stylesheet" href="/template/glx2021/assets/css/plugins.css">
    <link rel="stylesheet" href="/template/glx2021/assets/css/style.css?v=<?php echo filemtime(public_path().'/template/glx2021/assets/css/style.css') ?>">
    <link rel="stylesheet" href="/assert/css/lad-common.css">
    <link rel="stylesheet" href="/template/glx2021/css/custom.css?v=<?php echo filemtime(public_path().'/template/glx2021/css/custom.css') ?>">
    <script src="/assert/js/jquery-3.6.min.js"></script>
{{--    <script src="/application/_js/lib_base.js?v=1634016826"></script>--}}

    <!--    <link rel="stylesheet" href="/template/glxv2/css/style_glx.css">-->

    @yield("css")

    <style>
        .navbar{
            padding-top: 0px!important;
        }
    </style>

    <style>

        a {
            text-decoration: none!important;
        }

        .auth_cont {
            text-align: center;
        }

        .auth_zone {
            text-align: left;
            display: inline-block;
            margin: 30px 10px;
            max-width: 600px;
            /*height: 320px;*/
            border: 1px solid #ccc;
            background-color: lavender;
            border-radius: 5px;

            padding: 20px 30px;
        }
        .alert-danger {
            color: #721c24;
            background-color: transparent!important;
            border-color: transparent!important;
            color: red;
            font-style: italic;
            /* font-size: small; */
        }

        .alert {
            position: relative;
            padding: 3px 3px!important;;
            margin-bottom: 1rem!important;;
            margin-top: 1px!important;
            /* border-radius: 0.25rem; */
        }
        .content-wrapper {
            min-height: 600px;
        }

        .navbar-dark .navbar-nav .nav-link {
            color: white!important;
        }
    </style>

</head>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-Z364LBZ1KY"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-Z364LBZ1KY');
</script>

<body>
<div class="content-wrapper">
    <header class="wrapper bg-soft-primary">
        <nav class="navbar navbar-expand-lg center-nav transparent position-absolute navbar-dark caret-none" style="background: #101a2b">
            <div class="container flex-lg-row flex-nowrap align-items-center">
                <div class="navbar-brand w-100">
                    <a href="/">
                        <img class="logo-dark" src="/images/logo/gp_one.png"
                             srcset="/images/logo/gp_one.png 2x" alt=""/>
                        <img class="logo-light"
                             src="/images/logo/gp_one.png"
                             srcset="/images/logo/gp_one.png 2x" alt=""/>
                    </a>
                </div>
                <div class="navbar-collapse offcanvas-nav">
                    <div class="offcanvas-header d-lg-none d-xl-none">
                        <a href="/"><img class="logo-light" src="/images/logo/gp_one.png"
                                         srcset="/images/logo/gp_one.png" alt=""/></a>
                        <button type="button" class="btn-close btn-close-white offcanvas-close offcanvas-nav-close"
                                aria-label="Close"></button>
                    </div>
                    <ul class="navbar-nav">
                        <li class='nav-item dropdown'>
                            <a class="nav-link" href="/my-tree" >  Tạo Cây </a>
                        </li>
                        <li class='nav-item'>
                            <a class="nav-link" href='/my-tree?pid=js156958' > Cây mẫu</a>
                        </li>
{{--                        <li class='nav-item'>--}}
{{--                            <a class="nav-link" href='/vip-account' > Gói VIP</a>--}}
{{--                        </li>--}}
                        <li class='nav-item'>
                            <a class="nav-link" href='/tin-tuc' >  Thông tin</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/#intro-glx">  Giới thiệu</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/#intro-glx">  Tạo web riêng</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/member"> Tài khoản </a>
                        </li>

                        <li class="nav-item dropdown" style="display: none">
                            <a class="nav-link dropdown-toggle" href="#!">Pages
                                <span class="sub-arrow"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="dropdown"><a class="dropdown-item dropdown-toggle" href="#">Services</a>
                                    <ul class="dropdown-menu">
                                        <li class="nav-item"><a class="dropdown-item"
                                                                href="/template/glx2021/services.html">Services I</a>
                                        </li>
                                        <li class="nav-item"><a class="dropdown-item"
                                                                href="/template/glx2021/services2.html">Services II</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="dropdown"><a class="dropdown-item dropdown-toggle" href="#">About</a>
                                    <ul class="dropdown-menu">
                                        <li class="nav-item"><a class="dropdown-item"
                                                                href="/template/glx2021/about.html">About I</a></li>
                                        <li class="nav-item"><a class="dropdown-item"
                                                                href="/template/glx2021/about2.html">About II</a></li>
                                    </ul>
                                </li>

                            </ul>
                        </li>

                    </ul>
                    <!-- /.navbar-nav -->
                </div>
                <!-- /.navbar-collapse -->
                <div class="navbar-other w-100 d-flex ms-auto">

                    <ul class="navbar-nav flex-row align-items-center ms-auto" data-sm-skip="true">

                        <li class="nav-item dropdown language-select text-uppercase">
                            <a class="nav-link dropdown-item dropdown-toggle" href="#" role="button"
                               data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Vi</a>
                            <ul class="dropdown-menu">
                                <li class="nav-item"><a class="dropdown-item" href="#">En</a></li>
                                <li class="nav-item"><a class="dropdown-item" href="#">Jp</a></li>
                            </ul>
                        </li>

                        <!--                        <li class="nav-item"><a class="nav-link" data-toggle="offcanvas-info"><i-->
                        <!--                                        class="uil uil-info-circle"></i></a></li>-->

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
                    <a href="/"><img src="/images/logo/mytree1.png"
                                     srcset="/images/logo/mytree1.png"
                                     alt=""/></a>
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
                        <nav class="nav social social-white">
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

    @yield("content")
</div>

<!-- /.content-wrapper -->
<footer class="bg-dark text-inverse">
    <div class="container py-8 py-md-8">
        <div class="row gy-6 gy-lg-0">
            <div class="col-lg-6">
                <div class="widget">
                    <img class="mb-4" height="45"
                         src="/images/logo/mytree1.png"
                         srcset="/images/logo/mytree1.png" alt=""/>
                    <br>
                    CÔNG TY TNHH Công nghệ số Galaxy Việt Nam
                    <br>
                    Địa chỉ: 54 Nguyễn Đổng Chi, Nam Từ Liêm, Hà Nội
                    <br>

                    <p class="mb-4">© 2010 - <?php echo date("Y") ?> Galaxy Technology. All rights reserved.</p>
                    <nav class="nav social social-white">
{{--                        <a href="#"><i class="uil uil-twitter"></i></a>--}}
                        <a href="https://zalo.me/g/lwfebw839" >
                            Zalo
                        </a>

                        <a href="https://www.facebook.com/mytree.vn" target="_blank"><i class="uil uil-facebook-f"></i></a>
{{--                        <a href="#"><i class="uil uil-dribbble"></i></a>--}}
{{--                        <a href="#"><i class="uil uil-instagram"></i></a>--}}
                        <a href="https://www.youtube.com/watch?v=au0UI6yunCA&list=PL2ytCDlW-wDcV2gx1UqabbEFMb19R0viY"><i class="uil uil-youtube"></i></a>

                    </nav>
                    <!-- /.social -->
                </div>
                <!-- /.widget -->
            </div>
            <!-- /column -->
            <!--            <div class="col-md-4 col-lg-2 offset-lg-2">-->
            <!--                <div class="widget">-->
            <!--                    <h4 class="widget-title mb-3 text-white">Need Help?</h4>-->
            <!--                    <ul class="list-unstyled mb-0">-->
            <!--                        <li><a href="#">Support</a></li>-->
            <!--                        <li><a href="#">Get Started</a></li>-->
            <!--                        <li><a href="#">Terms of Use</a></li>-->
            <!--                        <li><a href="#">Privacy Policy</a></li>-->
            <!--                    </ul>-->
            <!--                </div>-->
            <!-- /.widget -->
            <!--            </div>-->
            <!-- /column -->
            <div class="col-md-4 col-lg-2">
                <div class="widget">
                    <h4 class="widget-title mb-3 text-white">Đọc thêm</h4>
                    <ul class="list-unstyled mb-0">
                        <li><a href="#">Về chúng tôi</a></li>
                        <li><a href="#">Dự án</a></li>
{{--                        <li><a href="#">Chi phí</a></li>--}}
                    </ul>
                </div>
                <!-- /.widget -->
            </div>
            <!-- /column -->
            <div class="col-md-4 col-lg-2">
                <div class="widget">
                    <h4 class="widget-title mb-3 text-white">Liên hệ</h4>
                    <a href="mailto:sale@glx.com.vn">sale@glx.com.vn</a><br/>
                    <a href="tel:0966616368">Tel, Zalo: 0966.61.63.68</a>
                    <address>24 Nguyễn Cơ Thạch, Nam Từ Liêm, Hà Nội</address>
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


<script src="/vendor/bootstrap4/bootstrap.min.js"></script>

<script src="/template/glx2021/assets/js/plugins.js"></script>
<script src="/template/glx2021/assets/js/theme.js"></script>

@yield("js")


</body>

</html>
