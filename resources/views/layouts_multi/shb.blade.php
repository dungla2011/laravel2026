<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Làm giàu thông minh, kinh doanh theo mạng Trieu phu 24h">
    <meta name="keywords" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>
        @yield("title")
    </title>
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cookie&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap"
          rel="stylesheet">

    <!-- Css Styles -->
    <link rel="stylesheet" href="/template/shop1/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="/template/shop1/css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="/template/shop1/css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="/template/shop1/css/jquery-ui.min.css" type="text/css">
    <link rel="stylesheet" href="/template/shop1/css/magnific-popup.css" type="text/css">
    <link rel="stylesheet" href="/template/shop1/css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="/template/shop1/css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="/template/shop1/css/style.css?v=1" type="text/css">

    <style>
        .img-responsive-glx{
            max-width: 100%;
        }

    </style>

    @yield('css')

</head>

<body>
<!-- Page Preloder -->
<div id="preloder">
    <div class="loader"></div>
</div>

<!-- Offcanvas Menu Begin -->
<div class="offcanvas-menu-overlay"></div>
<div class="offcanvas-menu-wrapper">
    <div class="offcanvas__close">+</div>
    <ul class="offcanvas__widget">
        <li><span class="icon_search search-switch"></span></li>
        <li><a href="#"><span class="icon_heart_alt"></span>
                <div class="tip">2</div>
            </a></li>
        <li><a href="#"><span class="icon_bag_alt"></span>
                <div class="tip">2</div>
            </a></li>
    </ul>
    <div class="offcanvas__logo">
        <a href="/"><img src="/template/shop1/img/logo_trieuphu24h.png" alt=""></a>
    </div>
    <div id="mobile-menu-wrap"></div>
    <div class="offcanvas__auth">
        <?php
        if(!getCurrentUserId()){
        ?>
            <a href="/login">Login</a>
        <?php
        }else{
        ?>
            <a href="/member">Tài khoản</a>
        <?php
        }
        ?>
    </div>
</div>
<!-- Offcanvas Menu End -->

<!-- Header Section Begin -->
<header class="header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-3 col-lg-2">
                <div class="header__logo">
                    <a href="/"><img src="/template/shop1/img/logo_trieuphu24h.png" alt=""></a>
                </div>
            </div>
            <div class="col-xl-6 col-lg-7">
                <nav class="header__menu">
                    <ul>
                        <li class="active"><a href="/">Trang chủ</a></li>
                        <li><a href="/member/network-marketing/shb">Mạng lưới của bạn</a></li>
                        <li><a href="/">Giới thiệu</a></li>

                        <li style="color: brown">
                            <?php
                            if(!getCurrentUserId()){
                            ?>
                            <a href="/login">Đăng nhập</a>
                            <?php
                            }else{
                            ?>
                            <a href="/member">Tài khoản</a>
                            <?php
                            }
                            ?>
                        </li>


                    </ul>
                </nav>
            </div>
{{--            <div class="col-lg-3">--}}
{{--                <div class="header__right">--}}
{{--                    <ul class="header__right__widget">--}}
{{--                        <li><span class="icon_search search-switch"></span></li>--}}
{{--                        <li><a href="#"><span class="icon_heart_alt"></span>--}}
{{--                                <div class="tip">2</div>--}}
{{--                            </a></li>--}}
{{--                        <li><a href="#"><span class="icon_bag_alt"></span>--}}
{{--                                <div class="tip">2</div>--}}
{{--                            </a></li>--}}
{{--                    </ul>--}}
{{--                </div>--}}
{{--            </div>--}}
        </div>
        <div class="canvas__open">
            <i class="fa fa-bars"></i>
        </div>
    </div>
</header>
<!-- Header Section End -->

@yield("content")

<!-- Footer Section Begin -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-7">
                <div class="footer__about">
                    <div class="footer__logo">
                        <a href="/"><img src="/template/shop1/img/logo_trieuphu24h.png" alt=""></a>
                    </div>
                    <p data-code-pos="ppp1682484587382">
                        Hỗ trợ bởi Công ty Công nghệ số Galaxy Việt Nam
                        <br>
                        HotLine: 09.0404.3689
                    </p>
                    <div class="footer__payment">
                        <a href="https://zalo.me/0904043689"><img style="width: 50px" src="/images/icon/icon-zalo.png" alt=""></a>
                    </div>
                </div>
            </div>
{{--            <div class="col-lg-2 col-md-3 col-sm-5">--}}
{{--                <div class="footer__widget">--}}
{{--                    <h6>Quick links</h6>--}}
{{--                    <ul>--}}
{{--                        <li><a href="#">About</a></li>--}}
{{--                        <li><a href="#">Blogs</a></li>--}}
{{--                        <li><a href="#">Contact</a></li>--}}
{{--                        <li><a href="#">FAQ</a></li>--}}
{{--                    </ul>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col-lg-2 col-md-3 col-sm-4">--}}
{{--                <div class="footer__widget">--}}
{{--                    <h6>Account</h6>--}}
{{--                    <ul>--}}
{{--                        <li><a href="#">My Account</a></li>--}}
{{--                        <li><a href="#">Orders Tracking</a></li>--}}
{{--                        <li><a href="#">Checkout</a></li>--}}
{{--                        <li><a href="#">Wishlist</a></li>--}}
{{--                    </ul>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col-lg-4 col-md-8 col-sm-8">--}}
{{--                <div class="footer__newslatter">--}}
{{--                    <h6>NEWSLETTER</h6>--}}
{{--                    <form action="#">--}}
{{--                        <input type="text" placeholder="Email">--}}
{{--                        <button type="submit" class="site-btn">Subscribe</button>--}}
{{--                    </form>--}}
{{--                    <div class="footer__social">--}}
{{--                        <a href="#"><i class="fa fa-facebook"></i></a>--}}
{{--                        <a href="#"><i class="fa fa-twitter"></i></a>--}}
{{--                        <a href="#"><i class="fa fa-youtube-play"></i></a>--}}
{{--                        <a href="#"><i class="fa fa-instagram"></i></a>--}}
{{--                        <a href="#"><i class="fa fa-pinterest"></i></a>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
        </div>
        <div class="row">
            <div class="col-lg-12">
                <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                <div class="footer__copyright__text">
                    <p>Copyright &copy; <script>document.write(new Date().getFullYear());</script> All rights reserved </p>
                </div>
                <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
            </div>
        </div>
    </div>
</footer>
<!-- Footer Section End -->

<!-- Search Begin -->
<div class="search-model">
    <div class="h-100 d-flex align-items-center justify-content-center">
        <div class="search-close-switch">+</div>
        <form class="search-model-form">
            <input type="text" id="search-input" placeholder="Search here.....">
        </form>
    </div>
</div>
<!-- Search End -->

<!-- Js Plugins -->
<script src="/template/shop1/js/jquery-3.3.1.min.js"></script>
<script src="/template/shop1/js/bootstrap.min.js"></script>
<script src="/template/shop1/js/jquery.magnific-popup.min.js"></script>
<script src="/template/shop1/js/jquery-ui.min.js"></script>
<script src="/template/shop1/js/mixitup.min.js"></script>
<script src="/template/shop1/js/jquery.countdown.min.js"></script>
<script src="/template/shop1/js/jquery.slicknav.js"></script>
<script src="/template/shop1/js/owl.carousel.min.js"></script>
<script src="/template/shop1/js/jquery.nicescroll.min.js"></script>
<script src="/template/shop1/js/main.js"></script>

@yield('js')



</body>

</html>
