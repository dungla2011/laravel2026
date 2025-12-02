<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @yield('title')
    </title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta-description')">
    <meta name="keywords" content="@yield('meta-keywords')">
    {{--    /images/icon/4s1k.png--}}

    <meta property="og:image" content="/images/icon/4s1k.png">
    <meta property="og:title" content="@yield('meta-description')">
    <meta property="og:description" content="@yield('meta-description')">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sriracha&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Charm&family=Sriracha&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Afacad&display=swap" rel="stylesheet">

    <link href="/template/fullcr/style.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

</head>

<?php
\App\Models\BlockUi::showCssHoverBlock();
?>
<style>

</style>

<body>

<div class="container-fluid p-0 top-nav-zone"
     style="">
    <div class="container p-0">
        <div class="banner">
            <a class="logo-mobi" href="/" style="">
                4Share1K
            </a>
            <i class="mobile_button fa fa-bars"></i>
        </div>

        <nav class="nav-cover" style="position: relative">
            <div class="login-pc">
                <?php
                if(getCurrentUserId()){
                    echo "<a href='/member'>  <i class='fa fa-user'> </i>  Tài khoản</a>";
                }
                else
                    echo "<a href='/login'> <i class='fa fa-unlock'> </i> Đăng nhập</a>";


                ?>



            </div>

            <a class="logo-pc" href="/" style="">
                <b>
                    4Share1K
                </b>
            </a>

            <ul class="ul-0 qqqq1111 ">


                <?php
                $mData = \App\Components\MenuHelper1::getMenuData();
                echo \App\Models\MenuTree::show_menu_recursive2024($mData, 3);

                \App\Models\BlockUi::showEditLink_("/admin/menu-tree/tree?pid=3&gid=1&open_all=1");

                ?>

            </ul>


        </nav>


        <div style="clear:both;"></div>
    </div>
</div>


<DIV STYLE="padding: 20px; background-color: dodgerblue; color: white; font-size: 120%; max-width: 800px; margin-top: 60px;
margin-left: auto; margin-right: auto;
        ; border-radius: 20px;
        ">
    THÔNG BÁO: DỊCH VỤ ĐÃ ĐƯỢC TÍCH HỢP VÀO 4SHARE.VN,
    <br>
    MỌI THÔNG TIN SỬ DỤNG CŨ TOÁN CŨ ĐÃ ĐƯỢC CHUYỂN SANG 4SHARE.VN
    <br>
    MỌI FILE CẦN TẢI ĐƯỢC LƯU TẠI 4SHARE.VN
    <BR>
    CẢM ƠN BẠN!
</DIV>
<div id="main_content">
@yield("content")
    <br>
</div>



<footer style="min-height: 80px;background-color: #444444 " class="footer">

    <div class="container">
        <div class="row">
            <div class="" style="padding: 10px">
                <a class="logo-mobi bottom" href="/">Dịch vụ 4Share1K</a>

                <div style="font-size: smaller">
                Powered by @4Share.vn
                    </div>
            </div>

        </div>

    </div>


</footer>

<a href="https://zalo.me/0904043689" id="linkzalo" target="_blank" rel="noopener noreferrer"><div id="fcta-zalo-tracking" class="fcta-zalo-mess">
        <span id="fcta-zalo-tracking"></span></div><div class="fcta-zalo-vi-tri-nut"><div id="fcta-zalo-tracking" class="fcta-zalo-nen-nut"><div id="fcta-zalo-tracking" class="fcta-zalo-ben-trong-nut"> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 460.1 436.6"><path fill="currentColor" class="st0" d="M82.6 380.9c-1.8-.8-3.1-1.7-1-3.5 1.3-1 2.7-1.9 4.1-2.8 13.1-8.5 25.4-17.8 33.5-31.5 6.8-11.4 5.7-18.1-2.8-26.5C69 269.2 48.2 212.5 58.6 145.5 64.5 107.7 81.8 75 107 46.6c15.2-17.2 33.3-31.1 53.1-42.7 1.2-.7 2.9-.9 3.1-2.7-.4-1-1.1-.7-1.7-.7-33.7 0-67.4-.7-101 .2C28.3 1.7.5 26.6.6 62.3c.2 104.3 0 208.6 0 313 0 32.4 24.7 59.5 57 60.7 27.3 1.1 54.6.2 82 .1 2 .1 4 .2 6 .2H290c36 0 72 .2 108 0 33.4 0 60.5-27 60.5-60.3v-.6-58.5c0-1.4.5-2.9-.4-4.4-1.8.1-2.5 1.6-3.5 2.6-19.4 19.5-42.3 35.2-67.4 46.3-61.5 27.1-124.1 29-187.6 7.2-5.5-2-11.5-2.2-17.2-.8-8.4 2.1-16.7 4.6-25 7.1-24.4 7.6-49.3 11-74.8 6zm72.5-168.5c1.7-2.2 2.6-3.5 3.6-4.8 13.1-16.6 26.2-33.2 39.3-49.9 3.8-4.8 7.6-9.7 10-15.5 2.8-6.6-.2-12.8-7-15.2-3-.9-6.2-1.3-9.4-1.1-17.8-.1-35.7-.1-53.5 0-2.5 0-5 .3-7.4.9-5.6 1.4-9 7.1-7.6 12.8 1 3.8 4 6.8 7.8 7.7 2.4.6 4.9.9 7.4.8 10.8.1 21.7 0 32.5.1 1.2 0 2.7-.8 3.6 1-.9 1.2-1.8 2.4-2.7 3.5-15.5 19.6-30.9 39.3-46.4 58.9-3.8 4.9-5.8 10.3-3 16.3s8.5 7.1 14.3 7.5c4.6.3 9.3.1 14 .1 16.2 0 32.3.1 48.5-.1 8.6-.1 13.2-5.3 12.3-13.3-.7-6.3-5-9.6-13-9.7-14.1-.1-28.2 0-43.3 0zm116-52.6c-12.5-10.9-26.3-11.6-39.8-3.6-16.4 9.6-22.4 25.3-20.4 43.5 1.9 17 9.3 30.9 27.1 36.6 11.1 3.6 21.4 2.3 30.5-5.1 2.4-1.9 3.1-1.5 4.8.6 3.3 4.2 9 5.8 14 3.9 5-1.5 8.3-6.1 8.3-11.3.1-20 .2-40 0-60-.1-8-7.6-13.1-15.4-11.5-4.3.9-6.7 3.8-9.1 6.9zm69.3 37.1c-.4 25 20.3 43.9 46.3 41.3 23.9-2.4 39.4-20.3 38.6-45.6-.8-25-19.4-42.1-44.9-41.3-23.9.7-40.8 19.9-40 45.6zm-8.8-19.9c0-15.7.1-31.3 0-47 0-8-5.1-13-12.7-12.9-7.4.1-12.3 5.1-12.4 12.8-.1 4.7 0 9.3 0 14v79.5c0 6.2 3.8 11.6 8.8 12.9 6.9 1.9 14-2.2 15.8-9.1.3-1.2.5-2.4.4-3.7.2-15.5.1-31 .1-46.5z"></path></svg></div><div id="fcta-zalo-tracking" class="fcta-zalo-text">Chat ngay</div></div></div></a>
<link rel="stylesheet" href="/vendor/zalo.css?x=1">
<script>
    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) )
    {document.getElementById("linkzalo").href="https://zalo.me/0904043689";}
</script>



</div>

</body>
</html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

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


    window.addEventListener('DOMContentLoaded', () => {
        const content = document.querySelector('#main_content');
        const footer = document.querySelector('.footer');

        function checkOverlap() {
            const contentRect = content.getBoundingClientRect();
            const footerRect = footer.getBoundingClientRect();

            console.log(contentRect.bottom , footerRect.top, contentRect.top , footerRect.bottom);

            // Kiểm tra xem vùng div có chồng lấn lên vùng sticky footer không
            const overlap = !(
                contentRect.bottom < footerRect.top ||
                contentRect.top > footerRect.bottom
            );

            if (overlap) {
                console.log('Vùng div đè lên vùng sticky footer');
                footer.style.position = 'static';
                // Thực hiện các hành động bạn muốn khi vùng div đè lên vùng sticky footer
            } else {
                console.log('Vùng div không đè lên vùng sticky footer');
                // Thực hiện các hành động bạn muốn khi vùng div không đè lên vùng sticky footer
            }
        }

        checkOverlap();

        window.addEventListener('scroll', checkOverlap);
        window.addEventListener('resize', checkOverlap);
    });


</script>

@yield('js')
