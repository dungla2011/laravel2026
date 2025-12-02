<!DOCTYPE html>
<html lang="en">

<head>
    <title>
        @yield("title")
    </title>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="@yield("og_desc")">

    <meta property="og:title" content="@yield("title")"/>
    <meta property="og:url" content="@yield("og_url")"/>
    <meta property="og:image" content="@yield("og_image")"/>
    <meta property="og:description" content="@yield("og_desc")"/>

    <link rel="shortcut icon" href="@yield("fav_icon")">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;1,100&family=Yanone+Kaffeesatz:wght@200&display=swap"
        rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css" rel="stylesheet">


    <link href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css" rel="stylesheet">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">


    <link rel="stylesheet" href="/template/d-hoa/style.css">

    <link rel="stylesheet" href="/admins/table_mng.css?v=<?php echo filemtime(public_path().'/admins/table_mng.css'); ?>">

    @yield("css")

    <style>
        .auth_cont {
            text-align: center;
            margin: 0 auto;
            min-height: 500px;
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
        .navbar-nav{
            background-color: royalblue;
            padding: 5px 5px 6px 20px;
            border-radius: 5px;
        }

        .navbar-nav > li > a{
            color: white;
        }

        .nav-div img {
            max-width: 180px;
        }
        .top-title{
            font-size: 110%;
        }

        #search-box , .search-mobi {
            display: none;
        }

        /* On screens that are 600px or less, set the background color to olive */
        @media screen and (max-width: 600px) {
            .search-mobi {
                display: block;
            }
            .carousel-caption h3 {
                font-size: 1.1em;
            }
            .carousel-caption p {
                font-size: 0.8em;
            }
            .top-title{
                font-size: 100%;
            }
            .navbar-nav {
                margin-top: 20px;
            }
            .top-nav .nav-item {
                border-bottom: 1px solid #ccc;
            }
            .navbar-nav{
                padding-left: 10px;
            }

            .top-menu {
                margin-left: 3px;
            }
            .top-nav .nav-item {
                margin-right: 5px;
            }
        }
        .dropdown-menu {
            padding: 10px 0px;
        }

        img.size-full{
            display: block;
            margin: 0 auto;
           width: 100%;
            max-width: 600px;
        }
        figcaption {
            text-align: center;
        }

        #search-box .sbox-zone {
            display: block; float: right; margin-bottom: 10px
        }
        .input-group-text{
            font-size: 0.95em;
        }

        .input-group-text input{
            font-size: small!important;
        }
        #input-val-search {
            color: red;
        }

        .sbox-zone{
            font-size: small;
        }


        /*GotoTop*/
        #toTopBtn {
            display: none;
            position: fixed;
            width: 30px;
            height: 30px;
            bottom: 20px;
            right: 30px;
            z-index: 99;
            font-size: 14px;
            border: none;
            outline: none;
            background-color: gray;
            color: white;
            cursor: pointer;
            /*padding-top: 5px;*/
            border-radius: 50%;
            border: 1px solid white;
        }

        #toTopBtn:hover {
            background-color: red;
        }

        .brc2 {
            white-space: nowrap;
            background-color: royalblue; display: inline-block;
            padding: 5px 10px; color: white!important;
            position: relative;
            height: 34px;

        }
        .brc2:after {
            position: absolute;
            right: -20px;
            top: 0px;
            content: "";
            width: 0;
            height: 0;
            border-top: 17px solid transparent;
            border-bottom: 17px solid transparent;
            border-left: 20px solid royalblue;
        }
        .brc2 a {
            color: white;
        }

    </style>

    <?php
    \App\Models\BlockUi::showCssHoverBlock();


    ?>
</head>

<body>

<?php

    $site = \App\Models\SiteMng::getInstance();
if($site->google_analytics_code	){
    $site->google_analytics_code = strip_tags($site->google_analytics_code);

    if($site->google_analytics_code && strlen($site->google_analytics_code) < 100){
        ?>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $site->google_analytics_code ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', '<?php echo $site->google_analytics_code ?>');
    </script>

<?php

    }

}



?>

<header class="">
    <div class="container-fluid top-header" style="">
        <div class="container qqqq1111">

            <i class="fa fa-envelope text-secondary"></i>

            <span class="top-title">
                <?php
                $ui = \App\Models\BlockUi::showEditButtonStatic('top-title-home-page');
                echo $ui->getSummary();
                ?>
            </span>

            <span style="float: right; font-size: 100%">
                <?php
                if(getCurrentUserId()){
                ?>
                <a href="/member">Thành Viên</a>
                <?php
                }else{
                ?>
                <a href="/member">Login</a>
                <?php
                }
                ?>
            </span>
        </div>
    </div>




    <nav class="nav navbar navbar-expand-lg bg-white sticky-top navbar-light shadow top-nav">

        <div class="container top-menu">

            <div class="qqqq1111 nav-div" style="min-width: 50px">
            <a class="navbar-brand" href="/">
                <?php
                $ui = \App\Models\BlockUi::showEditButtonStatic("logo-top-home-page");
                if($img = $ui->getThumbInImageListWithNoImg())
                    echo "<img src='$img'/>" ;
                ?>
            </a>
            </div>

            <a href="#" class="search-mobi" title="Tìm kiếm">
                <svg  xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-search toggle-search-box" viewBox="0 0 16 16">
                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                </svg>
            </a>
            <button class="navbar-toggler " type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">

                <ul class="navbar-nav mb-2 mb-lg-0 qqqq1111">
                    <?php
                    \App\Models\BlockUi::showEditLink_("/admin/menu-tree/tree?pid=3&gid=1&open_all=1");
                    ?>

                    <?php



                    $mData = \App\Components\MenuHelper1::getMenuData();
                    //                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
                    //                print_r($mData);
                    //                echo "</pre>";
                    echo \App\Components\MenuHelper1::show_menu_recursive2($mData,3);

                    ?>


                    <li class="nav-item">


                        <a href="#" class="nav-link" title="Tìm kiếm">
                            <svg  xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-search toggle-search-box" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                            </svg>
                        </a>
                    </li>

                </ul>


            </div>
        </div>
    </nav>
</header>


<div style="clear: both"></div>
<div class="container" id='search-box'>
    <div class="sbox-zone">
    <div class="input-group">

        <div class="input-group-text">
            <label for="radio_product" class="px-2">
            <input <?php if(request('stype') == 'product' || !request('stype')) echo 'checked'  ?>  type="radio" name="search_input" id="radio_product"> Sản phẩm
            </label>
            <label for="radio_service">
            <input <?php if(request('stype') == 'service') echo 'checked'  ?>  type="radio" name="search_input"  id="radio_service"> Dịch vụ
            </label>
        </div>


            <input placeholder="Tìm kiếm" id="input-val-search" type="search" value="<?php echo strip_tags(htmlspecialchars(strip_tags(request('search')))) ?>" class="form-control" />

        <button type="button" class="btn btn-primary" id="search_btn">
            <i class="fas fa-search"></i>
        </button>
        <button type="button" class="btn btn-default toggle-search-box">
            <i class="fas fa-times"></i>
        </button>
    </div>
    </div>
</div>

<div style="clear: both"></div>

<section class="content">

@yield("content")
</section>

<footer data-code-pos='ppp17060942042001'>
    <div class="container">

        <div class="row">
            <div class="col-sm-12 col-md-3 mt-1  qqqq1111">

                <?php
                $ui = \App\Models\BlockUi::showEditButtonStatic("footer-col-1");
                if($ui && $ui->getContent()){
                    echo $ui->getContent();
                }
                else{
                ?>
                <p class="fs-5">
                    ĐỊA CHỈ
                </p>
                <p class="company-info">
                    <i class="fa fa-home" aria-hidden="true"></i>
                    <span>
                            Công ty TNHH Công nghệ số Galaxy Vietnam
                        </span>
                </p>
                <p class="company-info">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>
                            54 Nguyễn Đổng Chi, Nam Từ Liêm, Hà Nội
                        </span>
                </p>
                <p class="company-info">
                    <i class="fa fa-clock" aria-hidden="true"></i>
                    <span>
                            Giờ mở cửa:
                        </span>
                </p>
                <?php
                }
                ?>
            </div>
            <div class="col-sm-12 col-md-3 mt-1 qqqq1111">
                <?php
                $ui = \App\Models\BlockUi::showEditButtonStatic("footer-col-2");
                if($ui && $ui->getContent())
                    echo $ui->getContent();
                else{
                ?>
                <p class="fs-5">
                    CHÍNH SÁCH
                </p>
                <p class="txt_link">
                        <span class="icon1">
                            <i class="fa fa-angle-right"></i>
                        </span>
                    <span class="txt1">
                            Lorem ipsum dolor sit amet.1
                        </span>
                </p>
                <p class="txt_link">
                        <span class="icon1">
                            <i class="fa fa-angle-right"></i>
                        </span>
                    <span class="txt1">
                            Lorem ipsum dolor sit amet.
                        </span>
                </p>
                <p class="txt_link">
                    <span class="icon1">
                        <i class="fa fa-angle-right"></i>
                    </span>
                    <span class="txt1">
                            Lorem ipsum dolor sit amet.
                    </span>
                </p>
                <?php
                }
                ?>

            </div>

            <div class="col-sm-12 col-md-3 mt-1 bao-gia qqqq1111">
                <?php
                $ui = \App\Models\BlockUi::showEditButtonStatic("footer-col-3");
                if($ui && $ui->getContent()){
                    echo $ui->getContent();
                }
                else{
                ?>
                <p class="fs-5">
                    BÁO GIÁ - TƯ VẤN - ĐẶT HÀNG
                </p>
                <p>
                    <i class="fa fa-phone"></i>
                    HotLine:
                </p>
                <p>
                    <i class="fa fa-envelope"></i>
                    Email:
                </p>
                <?php
                }
                ?>
            </div>

            <div class="col-sm-12 col-md-3 mt-1 gmap qqqq1111">
                <?php

                $ui = \App\Models\BlockUi::showEditButtonStatic("gmap-col-4");
                if($ui && $ui->getExtra()){
                    $gmap = $ui->getExtra();
                    echo $ui->getContent();
                    echo "<iframe src='$gmap'></iframe>";
                }
                else{
                ?>
                <p class="fs-5">
                    Hãy nhập Gmap
                </p>
                <?php
                }
                ?>
            </div>

        </div>
    </div>

    <style>
        .widget-call {
            position: fixed;
            bottom: 10px;
            left: 20px;
            z-index: 99999;
        }
        .widget-call .call-mobile2 {
            margin: 0 0 10px 0;
        }
        .widget-call .call-mobile {
            /*background: #ED1C24;*/
            /*height: 40px;*/
            /*line-height: 40px;*/
            padding: 0 0px 0 0px;
            /*border-radius: 40px;*/
            color: #fff;
        }
        .widget-call .call-mobile i.fa-phone {
            /*font-size: 20px;*/
            line-height: 40px;
            background: #B52026;
            border-radius: 100%;
            width: 40px;
            height: 40px;
            float: left;
        }
        .widget-call .call-mobile a {
            color: #fff;
            /*font-size: 18px;*/
            /*font-weight: bold;*/
            text-decoration: none;
            /*margin-right: 10px;*/
            /*padding-left: 10px;*/
        }

        .widget-call img{
            width: 40px;
        }

        .widget-call .call-mobile2 a {
            /*height: 40px;*/
            /*line-height: 40px;*/
            border-radius: 40px;
            color: #fff;
            /*background-color: #1E73BE;*/
            /*padding: 0 20px;*/
            /*display: inline-block;*/
        }


    </style>
    <div class="widget-call">
        <div class="call-mobile2 qqqq1111">
            <?php
            $ui = \App\Models\BlockUi::showEditButtonStatic("zalo-number");
            $phone = $ui && $ui->getSummary(1) ? $ui->getSummary(1) : 'Phone???';
            ?>
            <a target="_blank" rel="noopener noreferrer" href="http://zalo.me/<?php echo $phone ?>" class="button success">
                <img src="/template/gp1/images/icon-zalo.svg" alt="">
                </a>
        </div>
        <div class="call-mobile qqqq1111">
            <?php
            $ui = \App\Models\BlockUi::showEditButtonStatic("phone-number");
            $phone = $ui && $ui->getSummary(1) ? $ui->getSummary(1) : 'Phone???';
            ?>
            <a target="_blank"  id="callnowbutton" href="tel:<?php echo $phone ?>">
                <i class="fa fa-phone"></i>
                &nbsp;
                <span style="color: red; display: inline-block;font-size: 20px; font-weight: bold;text-shadow: 2px 0 #fff, -2px 0 #fff, 0 2px #fff, 0 -2px #fff,
             1px 1px #fff, -1px -1px #fff, 1px -1px #fff, -1px 1px #fff;  margin-top: 5px"><?php echo $phone ?>  </span>
            </a>
        </div>
    </div>

    <footer1>
        <div class="container">

            Copyright <?php echo date("Y") ?> ©
        </div>
    </footer1>
</footer>

</body>


@yield("js")

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        // make it as accordion for smaller screens
        if (window.innerWidth > 992) {

            document.querySelectorAll('.navbar .nav-item').forEach(function (everyitem) {

                everyitem.addEventListener('mouseover', function (e) {

                    let el_link = this.querySelector('a[data-bs-toggle]');

                    if (el_link != null) {
                        let nextEl = el_link.nextElementSibling;
                        el_link.classList.add('show');
                        nextEl.classList.add('show');
                    }

                });
                everyitem.addEventListener('mouseleave', function (e) {
                    let el_link = this.querySelector('a[data-bs-toggle]');

                    if (el_link != null) {
                        let nextEl = el_link.nextElementSibling;
                        el_link.classList.remove('show');
                        nextEl.classList.remove('show');
                    }


                })
            });

        }
        // end if innerWidth
    });
    // DOMContentLoaded  end

    document.addEventListener('DOMContentLoaded', function () {

        if(!document.getElementById('thumbnail-slider')){
            return;
        }

        new Splide('#thumbnail-slider', {
            fixedWidth: 244,
            fixedHeight: 300,
            gap: 25,
            rewind: true,
            pagination: false,
            cover: true,
        }).mount();
    });

    document.addEventListener('DOMContentLoaded', function () {
        if(!document.getElementById('thumbnail-slider2')){
            return;
        }
        new Splide('#thumbnail-slider2', {
            fixedWidth: 165,
            fixedHeight: 100,
            gap: 25,
            rewind: true,
            pagination: false,
            lazyLoad: 'sequential',
            //cover: true,
        }).mount();
    });


    $(function (){

        let valSearch = $("#input-val-search").val();

        $(".toggle-search-box").on('click', function (){
            if($("#search-box").is(":visible") && valSearch.length > 0)
                window.location.href = '<?php echo \LadLib\Common\UrlHelper1::getUriWithoutParam() ?>'
            $("#search-box").toggle(200);
        })

        if(valSearch){
            $("#input-val-search").css("font-weight", 'bold');
            $(".input-group-text").css("border", '1px solid #ccc');
            $("#search-box").css("display", 'block');
        }

        function  searchInfo(){
            console.log("Click search");
            let valSearch = $("#input-val-search").val();
            valSearch = valSearch.trim();
            if(!valSearch)
                return;

            if($("#radio_product").is(":checked")){
                console.log(" radio_product " +  valSearch);
                window.location.href = "/san-pham?stype=product&search=" + valSearch
            }
            if($("#radio_service").is(":checked")){
                console.log(" radio_service " + valSearch);
                window.location.href = "/tin-tuc?stype=service&search=" + valSearch;
            }
        }

        $("#input-val-search").keyup(function(e){
            var code = e.keyCode || e.which;
            if(code == 13) { //Enter keycode
                searchInfo();
            }
        })

        $("#search_btn").on('click', function (){
            searchInfo();
        })
    })

</script>


<script>

    //GotoTop
    document.addEventListener('DOMContentLoaded', function () {
        // Get the button
        let mybutton = document.getElementById("toTopBtn");

        // When the user scrolls down 20px from the top of the document, show the button
        window.onscroll = function () {
            scrollFunction()
        };


        function scrollFunction() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                mybutton.style.display = "block";
            } else {
                mybutton.style.display = "none";
            }
        }
    });
    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    }
</script>

<button onclick="topFunction()" id="toTopBtn" title="Go to top"> &#9650; </button>

</html>
