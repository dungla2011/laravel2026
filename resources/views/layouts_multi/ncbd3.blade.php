<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/template/sandbo/assets/img/favicon.png">
    <title>
        @yield('title')
    </title>
    <link rel="stylesheet" href="/template/sandbo/assets/css/plugins.css">
    <link rel="stylesheet" href="/template/sandbo/assets/css/style.css">
    <link rel="stylesheet" href="/template/sandbo/assets/css/colors/yellow.css">
    <link rel="preload" href="/template/sandbo/assets/css/fonts/thicccboi.css" as="style" onload="this.rel='stylesheet'">
</head>

<body>

<?php
$domain = \LadLib\Common\UrlHelper1::getDomainHostName();

$clink = \LadLib\Common\UrlHelper1::getFullUrl();

$linkEnglish = str_replace("$domain/event-register", "$domain/en/event-register", $clink);
$linkEnglish = str_replace("$domain/vi/", "$domain/en/", $linkEnglish);
$linkVn = str_replace("$domain/event-register", "$domain/vi/event-register", $clink);
$linkVn = str_replace("$domain/en/", "$domain/vi/", $linkVn);

?>

<div class="content-wrapper">
    <header class="wrapper">
        <nav class="navbar navbar-expand-lg center-nav transparent navbar-light" data-code-pos='ppp17321067663461'

        >
            <div class="container flex-lg-row flex-nowrap align-items-center"
                 style="border-bottom: 1px solid #eee"
            >
                <div class="navbar-brand w-100">
                    <a href="/">
                        <img src="/images/logo/ncbd-event.png" style="height: 50px; " alt="">
                    </a>
                </div>
                <div class="navbar-collapse offcanvas-nav">
                    <div class="offcanvas-header d-lg-none d-xl-none">
                        <a href="/">
                            <h2 style="color: white">
                            PlaneD
                            </h2>
                        </a>
                        <button type="button" class="btn-close btn-close-white offcanvas-close offcanvas-nav-close" aria-label="Close"></button>
                    </div>
                    <ul class="navbar-nav">

                        <li class="nav-item"><a class="nav-link" href="#">Projects</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="#">Partner</a>

                        </li>
                        <li class="nav-item"><a class="nav-link" href="#">About</a>

                            <!--/.dropdown-menu -->
                        </li>
                        <li class="nav-item"><a class="nav-link" href="#">Contact</a>

                            <!--/.dropdown-menu -->
                        </li>
                    </ul>
                    <!-- /.navbar-nav -->
                </div>
                <!-- /.navbar-collapse -->
                <div class="navbar-other w-100 d-flex ms-auto">
                    <ul class="navbar-nav flex-row align-items-center ms-auto" data-sm-skip="true">
                        <li class="nav-item dropdown language-select text-uppercase">
                            <a href="{{$linkVn}}" class="set_lang" style="">
                                <img style="height: 20px" src="/images/icon/flag_vi.png" alt="">
                            </a>
                            &nbsp;
                            <a href="{{$linkEnglish}}" class="set_lang" style="">
                                <img style="height: 20px" src="/images/icon/flag_en.png" alt="">
                            </a>
                        </li>
                        <li class="nav-item">
                            <?php
                            if(getCurrentUserId())
                                echo '<a href="/member"><b>Member</b></a>';
                            else
                                echo '<a href="/login"><b>Login</b></a>';
                            ?>
                        </li>
                        <li class="nav-item d-lg-none">
                            <div class="navbar-hamburger"><button class="hamburger animate plain" data-toggle="offcanvas-nav"><span></span></button></div>
                        </li>

                    </ul>
                    <!-- /.navbar-nav -->
                </div>
                <!-- /.navbar-other -->
                <div class="offcanvas-info text-inverse">
                    <button type="button" class="btn-close btn-close-white offcanvas-close offcanvas-info-close" aria-label="Close"></button>
                    <a href="./index.html"><img src="/template/sandbo/assets/img/logo-light.png" srcset="/template/sandbo/assets/img/logo-light@2x.png 2x" alt="" /></a>
                    <div class="mt-4 widget">
                        <p>Planned is a multipurpose which will be a great solution for your business.</p>
                    </div>
                    <!-- /.widget -->
                    <div class="widget">
                        <h4 class="widget-title text-white mb-3">Contact Info</h4>
                        <address> 69 Chùa Láng, Đống Đa, Hà nội </address>
                        <a href="mailto:first.last@email.com">info@email.com</a><br /> +00 (123) 456 78 90
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

    @yield('content')


    <!-- /section -->
</div>
<!-- /.content-wrapper -->
<footer class="bg-navy text-inverse">
    <div class="container pt-10 pt-md-17 pb-13 pb-md-15">
        <div class="d-lg-flex flex-row align-items-lg-center">
            <h3 data-code-pos='ppp17321478240981' class="display-4 mb-6 mb-lg-0 pe-lg-20 pe-xl-22 pe-xxl-25 text-white">Join our community by using our services and grow your business.</h3>
            <a href="#" class="btn btn-primary rounded-pill mb-0 text-nowrap">Try It For Free</a>
        </div>
        <!--/div -->
        <hr class="mt-5 mb-5" />
        <div class="row gy-6 gy-lg-0">
            <div class="col-md-4 col-lg-3">
                <div class="widget">
                    <h2 style="color: white">
                    PlaneD
                    </h2>
                    <p class="mb-4">© 2024 Planed. <br class="d-none d-lg-block" />All rights reserved.</p>
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
            <!-- /column -->
            <div class="col-md-4 col-lg-3">
                <div class="widget">
                    <h4 class="widget-title text-white mb-3">Get in Touch</h4>
                    <address class="pe-xl-15 pe-xxl-17">69 Chùa Láng, Đống Đa, Hà nội, Việt Nam</address>
                    <a href="mailto:#">info@email.com</a><br /> +00 (123) 456 78 90
                </div>
                <!-- /.widget -->
            </div>
            <!-- /column -->
            <div class="col-md-4 col-lg-3">
                <div class="widget">
                    <h4 class="widget-title text-white mb-3">Learn More</h4>
                    <ul class="list-unstyled  mb-0">
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Our Story</a></li>
                        <li><a href="#">Projects</a></li>
                        <li><a href="#">Terms of Use</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                    </ul>
                </div>
                <!-- /.widget -->
            </div>
            <!-- /column -->
            <div class="col-md-12 col-lg-3">
                <div class="widget">
                    <h4 class="widget-title text-white mb-3">Our Newsletter</h4>
                    <p class="mb-5">Subscribe to our newsletter to get our news & deals delivered to you.</p>
                    <div class="newsletter-wrapper">
                        <!-- Begin Mailchimp Signup Form -->
                        <div id="mc_embed_signup2">
                            <form action="https://elemisfreebies.us20.list-manage.com/subscribe/post?u=aa4947f70a475ce162057838d&amp;id=b49ef47a9a" method="post" id="mc-embedded-subscribe-form2" name="mc-embedded-subscribe-form" class="validate dark-fields" target="_blank" novalidate>
                                <div id="mc_embed_signup_scroll2">
                                    <div class="mc-field-group input-group form-floating">
                                        <input type="email" value="" name="EMAIL" class="required email form-control" placeholder="Email Address" id="mce-EMAIL2">
                                        <label for="mce-EMAIL2">Email Address</label>
                                        <input type="submit" value="Join" name="subscribe" id="mc-embedded-subscribe2" class="btn btn-primary">
                                    </div>
                                    <div id="mce-responses2" class="clear">
                                        <div class="response" id="mce-error-response2" style="display:none"></div>
                                        <div class="response" id="mce-success-response2" style="display:none"></div>
                                    </div> <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                                    <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_ddc180777a163e0f9f66ee014_4b1bcfa0bc" tabindex="-1" value=""></div>
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
        <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
    </svg>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous"></script>
<script src="/template/sandbo/assets/js/plugins.js"></script>
<script src="/template/sandbo/assets/js/theme.js"></script>
</body>

</html>
