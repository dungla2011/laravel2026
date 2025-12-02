<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSS -->
    <link rel="stylesheet" href="/_site/movie1/css/bootstrap.min.css">
    <link rel="stylesheet" href="/_site/movie1/css/splide.min.css">
    <link rel="stylesheet" href="/_site/movie1/css/slimselect.css">
    <link rel="stylesheet" href="/_site/movie1/css/plyr.css">
    <link rel="stylesheet" href="/_site/movie1/css/photoswipe.css">
    <link rel="stylesheet" href="/_site/movie1/css/default-skin.css">
    <link rel="stylesheet" href="/_site/movie1/css/main.css">

    <!-- Icon font -->
    <link rel="stylesheet" href="/_site/movie1/webfont/tabler-icons.min.css">

    <!-- Favicons -->
    <link rel="icon" type="image/png" href="icon/favicon-32x32.png" sizes="32x32">
    <link rel="apple-touch-icon" href="icon/favicon-32x32.png">

    <meta name="description" content="@yield('description')">
    <meta name="keywords" content="">
    <meta name="author" content="...">
    <title>@yield('title')</title>

    <style>
        .auth_cont {
            margin-top: 100px;
        }
        .item__cover img {
            max-height: 320px;
        }


    </style>
</head>

<body>
<!-- header -->
<header class="header" data-code-pos='ppp17458360414521'>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="header__content">
                    <!-- header logo -->
                    <a href="/" class="header__logo">
                        <img src="/_site/movie1/img/logo.svg" alt="">
                    </a>
                    <!-- end header logo -->

                    <!-- header nav -->
                    <ul class="header__nav">
                        <!-- dropdown -->

                        <!-- end dropdown -->


                        <!-- dropdown -->
                        <li class="header__nav-item">
                            <a class="header__nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Thể loại <i class="ti ti-chevron-down"></i></a>

                            <ul class="dropdown-menu header__dropdown-menu" data-code-pos='ppp17458360474151'>
                                <?php
                                $mfold = \App\Models\MediaFolder::where('status' ,'>', 0)->orderBy('name', 'asc')->get();
                                foreach ( $mfold AS $mf){

                                    //Slug name: $mf->name:

                                    $slg = \Illuminate\Support\Str::slug($mf->name);

                                    $lk = $mf->getLink1();

                                    ?>
                                    <li><a href="{{$lk}}">{{$mf->name}}</a></li>
                                <?php

                                }
                                ?>


                            </ul>
                        </li>
                        <!-- end dropdown -->
                        <li class="header__nav-item">
                            <a class="header__nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Phim lẻ </a>
                        </li>
                        <li class="header__nav-item">
                            <a class="header__nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Phim bộ </a>
                        </li>
                        <li class="header__nav-item">
                            <a class="header__nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Chiếu Rạp </a>
                        </li>

\                        <!-- dropdown -->
                        <li class="header__nav-item">
                            <a class="header__nav-link header__nav-link--more" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-dots"></i></a>

                            <ul class="dropdown-menu header__dropdown-menu">
                                <li><a href="signin.html">Sign in</a></li>
                                <li><a href="signup.html">Sign up</a></li>
                                <li><a href="forgot.html">Forgot password</a></li>
                                <li><a href="404.html">404 Page</a></li>
                                <li><a href="/admin">Admin</a></li>
                            </ul>
                        </li>
                        <!-- end dropdown -->
                    </ul>
                    <!-- end header nav -->

                    <!-- header auth -->
                    <div class="header__auth">
                        <form action="#" class="header__search">
                            <input class="header__search-input" type="text" placeholder="Search...">
                            <button class="header__search-button" type="button">
                                <i class="ti ti-search"></i>
                            </button>
                            <button class="header__search-close" type="button">
                                <i class="ti ti-x"></i>
                            </button>
                        </form>

                        <button class="header__search-btn" type="button">
                            <i class="ti ti-search"></i>
                        </button>

                        <!-- dropdown -->
                        <div class="header__lang">
                            <a class="header__nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">EN <i class="ti ti-chevron-down"></i></a>

                            <ul class="dropdown-menu header__dropdown-menu">
                                <li><a href="#">English</a></li>
                                <li><a href="#">Spanish</a></li>
                                <li><a href="#">French</a></li>
                            </ul>
                        </div>
                        <!-- end dropdown -->

                        <!-- dropdown -->
                        <div class="header__profile">
                            <a class="header__sign-in header__sign-in--user" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ti ti-user"></i>
                                <span>Nickname</span>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end header__dropdown-menu header__dropdown-menu--user">
                                <li><a href="profile.html"><i class="ti ti-ghost"></i>Profile</a></li>
                                <li><a href="profile.html"><i class="ti ti-stereo-glasses"></i>Subscription</a></li>
                                <li><a href="profile.html"><i class="ti ti-bookmark"></i>Favorites</a></li>
                                <li><a href="profile.html"><i class="ti ti-settings"></i>Settings</a></li>
                                <li><a href="#"><i class="ti ti-logout"></i>Logout</a></li>
                            </ul>
                        </div>
                        <!-- end dropdown -->
                    </div>
                    <!-- end header auth -->

                    <!-- header menu btn -->
                    <button class="header__btn" type="button">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                    <!-- end header menu btn -->
                </div>
            </div>
        </div>
    </div>
</header>

@yield('content')
<!-- end header -->


<!-- footer -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="footer__content">
                    <a href="/" class="footer__logo">
                        <img src="/_site/movie1/img/logo.svg" alt="">
                    </a>

                    <span class="footer__copyright">© HOTFLIX, 2019—2024 <br> Create by <a href="https://themeforest.net/user/dmitryvolkov/portfolio" target="_blank">Dmitry Volkov</a></span>

                    <nav class="footer__nav">
                        <a href="about.html">About Us</a>
                        <a href="contacts.html">Contacts</a>
                        <a href="privacy.html">Privacy policy</a>
                    </nav>

                    <button class="footer__back" type="button">
                        <i class="ti ti-arrow-narrow-up"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- end footer -->

<!-- plan modal -->
<div class="modal fade" id="plan-modal" tabindex="-1" aria-labelledby="plan-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal__content">
                <button class="modal__close" type="button" data-bs-dismiss="modal" aria-label="Close"><i class="ti ti-x"></i></button>

                <form action="#" class="modal__form">
                    <h4 class="modal__title">Select plan</h4>

                    <div class="sign__group">
                        <label for="fullname" class="sign__label">Name</label>
                        <input id="fullname" type="text" name="name" class="sign__input" placeholder="Full name">
                    </div>

                    <div class="sign__group">
                        <label for="email" class="sign__label">Email</label>
                        <input id="email" type="text" name="email" class="sign__input" placeholder="example@domain.com">
                    </div>

                    <div class="sign__group">
                        <label class="sign__label" for="value">Choose plan:</label>
                        <select class="sign__select" name="value" id="value">
                            <option value="35">Premium - $34.99</option>
                            <option value="50">Cinematic - $49.99</option>
                        </select>

                        <span class="sign__text">You can spend money from your account on the renewal of the connected packages, or to order cars on our website.</span>
                    </div>

                    <div class="sign__group">
                        <label class="sign__label">Payment method:</label>

                        <ul class="sign__radio">
                            <li>
                                <input id="type1" type="radio" name="type" checked="">
                                <label for="type1">Visa</label>
                            </li>
                            <li>
                                <input id="type2" type="radio" name="type">
                                <label for="type2">Mastercard</label>
                            </li>
                            <li>
                                <input id="type3" type="radio" name="type">
                                <label for="type3">Paypal</label>
                            </li>
                        </ul>
                    </div>

                    <button type="button" class="sign__btn sign__btn--modal">
                        <span>Proceed</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- end plan modal -->

<!-- JS -->
<script src="/_site/movie1/js/bootstrap.bundle.min.js"></script>
<script src="/_site/movie1/js/splide.min.js"></script>
<script src="/_site/movie1/js/slimselect.min.js"></script>
<script src="/_site/movie1/js/smooth-scrollbar.js"></script>
<script src="/_site/movie1/js/plyr.min.js"></script>
<script src="/_site/movie1/js/photoswipe.min.js"></script>
<script src="/_site/movie1/js/photoswipe-ui-default.min.js"></script>
<script src="/_site/movie1/js/main.js"></script>
</body>


</html>
