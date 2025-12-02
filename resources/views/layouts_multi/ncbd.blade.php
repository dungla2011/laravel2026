<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @yield('title')
    </title>

    <meta name="description" content="@yield('meta-description')">
    <meta name="keywords" content="@yield('meta-keywords')">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sriracha&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Charm&family=Sriracha&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Afacad&display=swap" rel="stylesheet">
</head>

<style>
    * {
        box-sizing: border-box;
        margin: 0px;
        padding: 0px;
        /*font-size: 95%;*/
        font-family: Arial, Helvetica, sans-serif
        /*font-family: 'Roboto', sans-serif;*/
        /*font-family: 'Yanone Kaffeesatz', sans-serif;*/
    }

    .set_lang {
        margin-right: 6px;
    }

     .footer {
         position: fixed;
         left: 0;
         bottom: 0;
         width: 100%;
         background-color: red;
         color: white;
         text-align: center;
     }


    a {
        text-decoration: none;
        color: #272727;

    }

    nav {
        /*border-bottom: 1px solid #ccc;*/
    }

    nav .ul-0 {
        display: flex;
        /*font-size: 90%;*/
    }

    nav ul {
        padding: 0px;
        margin: 0px;
    }

    nav .ul-0 > li {
        display: inline-block;
        /*color: white;*/

    }

    nav .ul-0 li {
        position: relative;
        list-style: none;
        /*border-bottom: 1px dashed #ccc;*/
        /*border-left: 1px dashed #ccc;*/

        /*background-color: #eee;*/
        font-family: 'Afacad', cursive;

    }

    nav .ul-0 li a {
        font-size: 120%;
        padding: 10px;
        /*line-height: 20px;*/
        display: inline-block;
        /*color: white;*/
        font-family: 'Afacad', cursive;

    }

    nav .ul-0 .sub-menu {
        display: none;
        position: absolute;
        top: 0;
        left: 100%;
        width: 200px;
        z-index: 100000;
        /*background-color: darkblue;*/
    }

    nav .ul-0 li:hover > .sub-menu {
        display: block;
        transition-delay: 0.1s;
    }

    nav .ul-0 > li {
        border-bottom: 2px solid transparent;
    }

    nav .ul-0 > li:hover:not(:first-child):not(:last-child) {
        /*background-color: #eee;*/
        border-bottom: 2px solid brown;
        transition-delay: 0.1s;
        transition-timing-function: ease-in;
    }


    nav li:hover > a {
        font-weight: bold;
    }

    nav .ul-0 > li > .sub-menu {
        top: 48px;
        left: 0;
    }

    nav .ul-0 > li li:hover {
        background-color: lavender;
    }

    .sub-menu {
        box-shadow: 1px 1px 1px 1px #ccc;
    }


    .sub-menu li {
        border-bottom: 1px solid #eee;
        border-left: 1px solid #eee;
        background-color: white;

    }

    nav i.more {
        display: none;
        font-size: 130%;
    }

    nav.nav-cover {
        /*background-color: darkblue;*/
        position: sticky;
        top: 0px;
        display: block;
    }

    .mobile_button {

        display: none;
    }

    div.banner {
        z-index: 1000;
        /*!* Để luôn ontop khi kéo xuống*!*/
        /*position: sticky;*/
        /*top: 0px;*/
        display: none;
        /*background-color: #282828;*/
        /*color: greenyellow;*/
        height: 39px;
        font-size: 120%;
        padding: 10px;

    }

    .logo-pc {
        /*text-align: center;*/
        /*width: 150px;*/
    }

    .logo-pc a {
        padding: 0px !important;
        padding-right: 20px !important;
    }

    .login-pc {
        position: absolute;
        right: 10px;
        top: 14px;
    }

    .login-pc a {
        font-size: 120%;
        font-family: 'Afacad', cursive!important;
    }
    .auth_zone {
        max-width: 500px;
        margin: 50px auto;
        border: 1px solid #eee;
        padding: 30px;
        border-radius: 10px;
        background-color: white;
        box-shadow: 1px 1px 1px 1px #cccccccc;
    }

    .slide-home{
        position: relative;
        height: 500px;
        background-image: url('https://thienchualanh.com/wp-content/uploads/2020/04/file-chat-luong-cao.jpg');
        /*background-image: url('/images/tmp/trung-tam-toan.png');*/
        /*background-image: url('/images/tmp/baner-toan2.png');*/
        /*background-image: url('/images/tmp/baner-toan6.png');*/
        /*background-image: url('/images/tmp/baner-toan9.png');*/
        /*background-image: url('/images/tmp/baner-toan6.png');*/
        background-image: url('/images/tmp/board01.jpg');

        background-size: cover;
        background-position: top;

        background-position: center;
        /*background-position: bottom;*/
        background-repeat: no-repeat;
        z-index: 100;
    }

    .slogan i {
        font-size: 80%;
    }
    .slogan {
        position: absolute; top: 40%; left: 50%; transform: translate(-50%,-50%);
        font-size: 180%;
        width: 80%;
        text-align: center;
        /*text-shadow: 1px 1px #aaa;*/
        color: darkorange;
        /*color: transparent;*/
        font-family: 'Sriracha', cursive;
        /*font-family: 'Charm', cursive;*/


    }

    .clearfix::after {
        content: "";
        clear: both;
        display: table;
    }

    @media screen and (max-width: 800px) {

        .slogan {
            top: 50%; font-size: 100%
        }

        nav .ul-0 {
            /*  nav : trở lại block để responsive ok */
            display: block;
        }

        .login-pc {
            display: none;
        }

        nav .ul-0 .sub-menu {
            display: none;
            position: static;
            /*top: 0;*/
            /*left: 100%;*/
            width: 100%;
            /*background-color: black;*/
            padding-left: 10px;

        }

        nav .ul-0 li {
            display: block;

            /*color: white;*/
            border-left: 0px solid red;
        }

        nav i.more {
            display: inline;
            position: absolute;
            right: 10px;
            top: 10px;
            cursor: pointer;
            float: right;
        }

        .mobile_button {
            display: inline-block;
            position: absolute;
            right: 20px;
            top: 10px
        }

        div.banner {
            height: 46px;
            display: block;
            position: sticky;
            top: 0px;
            /*background-color: lavender;*/
            border-bottom: 1px solid #ccc;
        }

        nav.nav-cover {
            display: none;
            /*position: static;*/
        }

        .logo-pc {
            display: none !important;
        }

        div.banner {
            padding: 0px;
        }

        div.banner img {
            height: 100%;
            border: 2px solid white;
        }
        .slide-home{
            height: 200px;
        }

    }

    footer {
        /*position: fixed;*/
        /*left: 0;*/
        bottom: 0;
        width: 100%;

        background-color: #727272;
        color: white;
        text-align: center;
        /*padding: 10px;*/
    }

    .content {
        min-height: 500px;
        padding: 20px;
        /*background-color: snow;*/
        border-left: 1px solid snow;
        border-right: 1px solid snow;
    }

    .top-nav-zone{
        box-shadow: 0px -4px 3px 5px #999999; z-index: 10000; border-bottom: 0px solid #ddd; position: sticky; top: 0px; background-color: white
    }
    .auth_zone input {
        margin: 10px 0px;
    }
    .auth_zone label {
        font-size: 90%;
    }

    li.logo-pc a {
        display: block;;
        margin: 6px 0px; color: darkorange; font-weight: bolder; font-style: italic
    }
    a.logo-mobi {
        display: inline-block; padding: 10px; color: darkorange; font-weight: bolder; font-style: italic
    }
    a.logo-mobi.bottom {
        padding: 0px
    }
    .mobile_button {
        top: 20px;
        right: 25px
    }
    @media screen and (max-width: 800px) {
        div.banner {
            height: 62px;
        }
    }

</style>


<body>
<?php
$domain = \LadLib\Common\UrlHelper1::getDomainHostName();

$clink = \LadLib\Common\UrlHelper1::getFullUrl();

$linkEnglish = str_replace("$domain/event-register", "$domain/en/event-register", $clink);
$linkEnglish = str_replace("$domain/vi/", "$domain/en/", $linkEnglish);
$linkVn = str_replace("$domain/event-register", "$domain/vi/event-register", $clink);
$linkVn = str_replace("$domain/en/", "$domain/vi/", $linkVn);

?>


<div class="container-fluid p-0 top-nav-zone"
     style="">
    <div class="container p-0">
        <div class="banner">
            <a class="logo-mobi" href="/" style="">
                <img src="/images/logo/ncbd-event.png" style="height: 50px; " alt="">
            </a>
            <div style="position: absolute; top: 18px; right: 60px; font-size: 90%">

                <a href="{{$linkVn}}" class="set_lang" style="">
                    <img style="height: 22px" src="/images/icon/flag_vi.png" alt="">
                </a>

                <a href="{{$linkEnglish}}" class="set_lang" style="">
                    <img style="height: 22px" src="/images/icon/flag_en.png" alt="">
                </a>

                <a href="/member">
            Thành viên
                </a>
            </div>
            <i class="mobile_button fa fa-bars"></i>
        </div>

        <nav class="nav-cover" style="position: relative" data-code-pos='ppp17301154006411'>
            <div class="login-pc">


                <a href="{{$linkVn}}" class="set_lang" >
                    <img style="height: 22px" src="/images/icon/flag_vi.png" alt="">
                </a>
                <a href="{{$linkEnglish}}" class="set_lang" style="">
                    <img style="height: 22px" src="/images/icon/flag_en.png" alt="">
                </a>

                <?php
                if(getCurrentUserId()){
                    echo "<a href='/member'>  <i class='fa fa-user'> </i>  Thành viên</a>";
                }
                else
                    echo "<a href='/login'> <i class='fa fa-unlock'> </i> Đăng nhập</a>";
                ?>

            </div>

            <ul class="ul-0">

                <li class="logo-pc">
                    <a href="/" style="">
                        <img src="/images/logo/ncbd-event.png" style="height: 50px" alt="">
                    </a>
                </li>

            </ul>


        </nav>
        <div style="clear:both;"></div>
    </div>
</div>



@yield("content")




{{--<a href="https://zalo.me/0904043689" id="linkzalo" target="_blank" rel="noopener noreferrer"><div id="fcta-zalo-tracking" class="fcta-zalo-mess">--}}
{{--        </div><div class="fcta-zalo-vi-tri-nut"><div id="fcta-zalo-tracking" class="fcta-zalo-nen-nut"><div id="fcta-zalo-tracking" class="fcta-zalo-ben-trong-nut"> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 460.1 436.6"><path fill="currentColor" class="st0" d="M82.6 380.9c-1.8-.8-3.1-1.7-1-3.5 1.3-1 2.7-1.9 4.1-2.8 13.1-8.5 25.4-17.8 33.5-31.5 6.8-11.4 5.7-18.1-2.8-26.5C69 269.2 48.2 212.5 58.6 145.5 64.5 107.7 81.8 75 107 46.6c15.2-17.2 33.3-31.1 53.1-42.7 1.2-.7 2.9-.9 3.1-2.7-.4-1-1.1-.7-1.7-.7-33.7 0-67.4-.7-101 .2C28.3 1.7.5 26.6.6 62.3c.2 104.3 0 208.6 0 313 0 32.4 24.7 59.5 57 60.7 27.3 1.1 54.6.2 82 .1 2 .1 4 .2 6 .2H290c36 0 72 .2 108 0 33.4 0 60.5-27 60.5-60.3v-.6-58.5c0-1.4.5-2.9-.4-4.4-1.8.1-2.5 1.6-3.5 2.6-19.4 19.5-42.3 35.2-67.4 46.3-61.5 27.1-124.1 29-187.6 7.2-5.5-2-11.5-2.2-17.2-.8-8.4 2.1-16.7 4.6-25 7.1-24.4 7.6-49.3 11-74.8 6zm72.5-168.5c1.7-2.2 2.6-3.5 3.6-4.8 13.1-16.6 26.2-33.2 39.3-49.9 3.8-4.8 7.6-9.7 10-15.5 2.8-6.6-.2-12.8-7-15.2-3-.9-6.2-1.3-9.4-1.1-17.8-.1-35.7-.1-53.5 0-2.5 0-5 .3-7.4.9-5.6 1.4-9 7.1-7.6 12.8 1 3.8 4 6.8 7.8 7.7 2.4.6 4.9.9 7.4.8 10.8.1 21.7 0 32.5.1 1.2 0 2.7-.8 3.6 1-.9 1.2-1.8 2.4-2.7 3.5-15.5 19.6-30.9 39.3-46.4 58.9-3.8 4.9-5.8 10.3-3 16.3s8.5 7.1 14.3 7.5c4.6.3 9.3.1 14 .1 16.2 0 32.3.1 48.5-.1 8.6-.1 13.2-5.3 12.3-13.3-.7-6.3-5-9.6-13-9.7-14.1-.1-28.2 0-43.3 0zm116-52.6c-12.5-10.9-26.3-11.6-39.8-3.6-16.4 9.6-22.4 25.3-20.4 43.5 1.9 17 9.3 30.9 27.1 36.6 11.1 3.6 21.4 2.3 30.5-5.1 2.4-1.9 3.1-1.5 4.8.6 3.3 4.2 9 5.8 14 3.9 5-1.5 8.3-6.1 8.3-11.3.1-20 .2-40 0-60-.1-8-7.6-13.1-15.4-11.5-4.3.9-6.7 3.8-9.1 6.9zm69.3 37.1c-.4 25 20.3 43.9 46.3 41.3 23.9-2.4 39.4-20.3 38.6-45.6-.8-25-19.4-42.1-44.9-41.3-23.9.7-40.8 19.9-40 45.6zm-8.8-19.9c0-15.7.1-31.3 0-47 0-8-5.1-13-12.7-12.9-7.4.1-12.3 5.1-12.4 12.8-.1 4.7 0 9.3 0 14v79.5c0 6.2 3.8 11.6 8.8 12.9 6.9 1.9 14-2.2 15.8-9.1.3-1.2.5-2.4.4-3.7.2-15.5.1-31 .1-46.5z"></path></svg></div><div id="fcta-zalo-tracking" class="fcta-zalo-text">Chat ngay</div></div></div></a>--}}
{{--<link rel="stylesheet" href="/vendor/zalo.css?x=1">--}}
{{--<script>--}}
{{--    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) )--}}
{{--    {document.getElementById("linkzalo").href="https://zalo.me/0904043689";}--}}
{{--</script>--}}


</div>

<footer class="footer" style="background-color: #034671 " class="p-3">

    <div class="container" style="padding: 10px 0px">
        Planed - Event Management System
    </div>



</footer>

</body>
</html>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>

    window.onload = function () {
        document.querySelectorAll("i.more").forEach(function (elm) {

            elm.addEventListener('click', function () {
                console.log("Click ...");
                let sub = this.parentElement.querySelector('ul.sub-menu')
                // var x = document.getElementById("myDIV");
                if (sub.style.display === "none" || !sub.style.display) {
                    sub.style.display = "block";
                } else {
                    sub.style.display = "none";
                }
            })
        })

        document.querySelector('.mobile_button').addEventListener('click', function () {
            // return;
            let divNav = document.querySelector('.nav-cover')
            // var x = document.getElementById("myDIV");
            console.log("Click 1", divNav.style.display);
            if (divNav.style.display === "none" || !divNav.style.display) {
                console.log("Click 2");
                divNav.style.display = "block";
            } else {
                divNav.style.display = "none";
                console.log("Click 3");
            }

            //Về top để menu show được lên khi ở chế độ sticky
            window.scrollTo(0, 0);

        })

        //Ở Mobile nếu đang ẩn menu, thì phóng to ra Wide size, sẽ phải hiện lại menu
        window.addEventListener("resize", function () {
            let width = document.body.clientWidth;
            if (width > 800) {
                console.log("resize > 800, show");
                document.querySelector('.nav-cover').style.display = "block";
            }else{
                document.querySelector('.nav-cover').style.display = "none";
            }
        });

        //Khi scrool, đang ở mobile thì ẩn menu đi
        // window.onscroll = function (e) {
        //     console.log("window.scrollY " , window.scrollY);
        //     if(window.scrollY > 10) {
        //         console.log(" > 10 ...");
        //         if(document.body.clientWidth < 800)
        //             document.querySelector('div.nav-cover').style.display = 'none';
        //     }
        // };

    }

</script>

@yield('js')
