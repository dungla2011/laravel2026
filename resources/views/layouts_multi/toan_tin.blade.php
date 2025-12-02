<!DOCTYPE html>
<html lang="en">
<head data-code-pos='ppp17388140235041'>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

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
    <link href="/_site/tailieu/common.css?v=1" rel="stylesheet">

</head>

<style>

    .search_mobile {
        display: none;
    }
    .form_search_1 {
        display: inline-block;
    }

    @media only screen and (max-width: 800px) {
        .search_mobile {
            display: block;
            margin-top: 20px;
        }
        .form_search_1{
            display: none;
        }
    }

</style>

@yield('css')

<?php
function searchFileDoc($cls = '')
{
    //Neu

    ?>

<div class="input-group " data-code-pos='ppp17388147771741' style="padding-left: 20px; margin-top: 4px">

    <form action="/search" style="margin-left: 15px; border: 0px solid red; margin: 0 auto" class="form_search_top">
        <img src="/images/icon/new_blink3.gif" style="height: 25px; margin-bottom: 2px">
        <input data-code-pos="ppp173648693501" placeholder="Tìm, ví dụ: toán, tin, bài tập..." name="search_string" value="{{ request('search_string')  }}"
               class="search-top {{$cls}} " style="border: 1px solid red; border-right: 0px; font-size: 14px" type="text"><button type="submit" class="search-top" style="border: 1px solid red;">
            <input type="hidden" name="exactly" value="1">
            <input type="hidden" name="sort_by" value="new">
            <i style="color: red" class="fa fa-search"></i>
        </button>
    </form>
</div>
    <?php
}
?>

<body data-code-pos='ppp17388140377421'>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-64CB9TD0FB"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-64CB9TD0FB');
</script>
<div class="container-fluid p-0 top-nav-zone" data-code-pos='ppp17334098960841'
     style="">
    <div class="container p-0">
        <div class="banner">
            <a class="logo-mobi" href="/" style="">
                TàiLiệuChuẩn.net
            </a>
            <i class="mobile_button fa fa-bars"></i>
        </div>

        <nav class="nav-cover" style="position: relative" data-code-pos='ppp17334098996721'>
            <div class="login-pc">
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
                        TàiLiệuChuẩn.net
                    </a>
                </li>
                <li data-code-pos='ppp17334099050261'>
                    <a href="#">Lớp</a>

                    <i class="more fa fa-caret-down"></i>
                    <ul class="sub-menu" data-code-pos='ppp17334099104691'>
                        <?php
                        $mCat = \App\Models\MyDocumentCat::where(['parent_id'=> 27])->orderBy('orders', 'asc') ->get();
                        foreach ($mCat AS $cat){

                            $link = "/tai-lieu/danh-muc?fid=$cat->id";
                        ?>
                        <li>
                            <a href="{{$link}}">{{$cat->name}}</a>
                        </li>
                        <?php
                        }
                        ?>
                    </ul>
                </li>
                <li data-code-pos='ppp17334099172231'>
                    <a href="#">Môn học</a>

                    <i class="more fa fa-caret-down"></i>
                    <ul class="sub-menu">
                        <?php
                        $mCat = \App\Models\MyDocumentCat::where(['parent_id'=> 24])->get();
                        foreach ($mCat AS $cat){

                            $link = "/tai-lieu/danh-muc?fid=$cat->id";
                            ?>
                        <li>
                            <a href="{{$link}}">{{$cat->name}}</a>
                        </li>
                            <?php
                        }
                        ?>
                    </ul>

                </li>

                <li data-code-pos='ppp17334099233611'><a href="#">Thể loại</a>

                    <i class="more fa fa-caret-down"></i>
                    <ul class="sub-menu">
                        <?php

                        $mCat = \App\Models\MyDocumentCat::where(['parent_id'=> 23])->get();
                        foreach ($mCat AS $cat){

                            $link = "/tai-lieu/danh-muc?fid=$cat->id";
                            ?>
                        <li>
                            <a href="{{$link}}">{{$cat->name}}</a>
                        </li>
                            <?php
                        }
                        ?>
                    </ul>

                </li>

                <li>
                    <a href="#">Kiểm tra</a>
                    <ul class="sub-menu">
                        <li>
                            <a href="/quiz/toan-tu-duy-tuyen-chon-toan-dien-tieu-hoc"> Toán tư duy Tiểu học</a>
                        </li>

                    </ul>
                </li>
                <li><a href="#">Game</a>
                    <i class="more fa fa-caret-down"></i>
                    <ul class="sub-menu">
                        <li>
                            <a href="/game/duoi-hinh-bat-chu"> Đuổi hình Bắt chữ</a>
                        </li>

                        <li>
                            <a href="/game/tap-danh-may"> Tập đánh máy</a>
                        </li>
                    </ul>

                </li>
                <li><a href="#">Liên hệ</a></li>
                <li>
                    <?php
//                    if(isDebugIp())


                    searchFileDoc('top1');

                    ?>
                </li>
            </ul>


        </nav>
        <div style="clear:both;"></div>
    </div>
</div>

<div class="search_mobile">
<?php
searchFileDoc('');
?>
</div>

@yield("content")



<footer style="min-height: 100px;background-color: #444444 " class="py-3">

    <div class="container">
        <div class="row">
            <div class="col-sm-4" style="text-align: left">
                <a class="logo-mobi bottom" href="/">TàiLiệuChuẩn.net</a>
            </div>
            <div class="col-sm-4" style="text-align: left">
                <p>Liên hệ</p>

            </div>
            <div class="col-sm-4" style="text-align: left">
                <p>Bản đồ</p>
            </div>

        </div>

    </div>


</footer>

<a href="https://zalo.me/0904043689" id="linkzalo" target="_blank" rel="noopener noreferrer"><div id="fcta-zalo-tracking" class="fcta-zalo-mess">
        <span id="fcta-zalo-tracking">Hỗ trợ</span></div><div class="fcta-zalo-vi-tri-nut"><div id="fcta-zalo-tracking" class="fcta-zalo-nen-nut"><div id="fcta-zalo-tracking" class="fcta-zalo-ben-trong-nut"> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 460.1 436.6"><path fill="currentColor" class="st0" d="M82.6 380.9c-1.8-.8-3.1-1.7-1-3.5 1.3-1 2.7-1.9 4.1-2.8 13.1-8.5 25.4-17.8 33.5-31.5 6.8-11.4 5.7-18.1-2.8-26.5C69 269.2 48.2 212.5 58.6 145.5 64.5 107.7 81.8 75 107 46.6c15.2-17.2 33.3-31.1 53.1-42.7 1.2-.7 2.9-.9 3.1-2.7-.4-1-1.1-.7-1.7-.7-33.7 0-67.4-.7-101 .2C28.3 1.7.5 26.6.6 62.3c.2 104.3 0 208.6 0 313 0 32.4 24.7 59.5 57 60.7 27.3 1.1 54.6.2 82 .1 2 .1 4 .2 6 .2H290c36 0 72 .2 108 0 33.4 0 60.5-27 60.5-60.3v-.6-58.5c0-1.4.5-2.9-.4-4.4-1.8.1-2.5 1.6-3.5 2.6-19.4 19.5-42.3 35.2-67.4 46.3-61.5 27.1-124.1 29-187.6 7.2-5.5-2-11.5-2.2-17.2-.8-8.4 2.1-16.7 4.6-25 7.1-24.4 7.6-49.3 11-74.8 6zm72.5-168.5c1.7-2.2 2.6-3.5 3.6-4.8 13.1-16.6 26.2-33.2 39.3-49.9 3.8-4.8 7.6-9.7 10-15.5 2.8-6.6-.2-12.8-7-15.2-3-.9-6.2-1.3-9.4-1.1-17.8-.1-35.7-.1-53.5 0-2.5 0-5 .3-7.4.9-5.6 1.4-9 7.1-7.6 12.8 1 3.8 4 6.8 7.8 7.7 2.4.6 4.9.9 7.4.8 10.8.1 21.7 0 32.5.1 1.2 0 2.7-.8 3.6 1-.9 1.2-1.8 2.4-2.7 3.5-15.5 19.6-30.9 39.3-46.4 58.9-3.8 4.9-5.8 10.3-3 16.3s8.5 7.1 14.3 7.5c4.6.3 9.3.1 14 .1 16.2 0 32.3.1 48.5-.1 8.6-.1 13.2-5.3 12.3-13.3-.7-6.3-5-9.6-13-9.7-14.1-.1-28.2 0-43.3 0zm116-52.6c-12.5-10.9-26.3-11.6-39.8-3.6-16.4 9.6-22.4 25.3-20.4 43.5 1.9 17 9.3 30.9 27.1 36.6 11.1 3.6 21.4 2.3 30.5-5.1 2.4-1.9 3.1-1.5 4.8.6 3.3 4.2 9 5.8 14 3.9 5-1.5 8.3-6.1 8.3-11.3.1-20 .2-40 0-60-.1-8-7.6-13.1-15.4-11.5-4.3.9-6.7 3.8-9.1 6.9zm69.3 37.1c-.4 25 20.3 43.9 46.3 41.3 23.9-2.4 39.4-20.3 38.6-45.6-.8-25-19.4-42.1-44.9-41.3-23.9.7-40.8 19.9-40 45.6zm-8.8-19.9c0-15.7.1-31.3 0-47 0-8-5.1-13-12.7-12.9-7.4.1-12.3 5.1-12.4 12.8-.1 4.7 0 9.3 0 14v79.5c0 6.2 3.8 11.6 8.8 12.9 6.9 1.9 14-2.2 15.8-9.1.3-1.2.5-2.4.4-3.7.2-15.5.1-31 .1-46.5z"></path></svg></div><div id="fcta-zalo-tracking" class="fcta-zalo-text">Chat ngay</div></div></div></a>
<link rel="stylesheet" href="/vendor/zalo.css?x=1">
<script>
    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) )
    {document.getElementById("linkzalo").href="https://zalo.me/0904043689";}
</script>


<div style="text-align: center; padding: 5px; background-color: #1a252f;  font-size: 70%">
    <a href="//galaxycloud.vn" target="_blank" style="color: #aaa;">
        <i>
        Powered By GalaxyCloud.vn @ 2010-<?php echo date("Y") ?>
        </i>
    </a>
</div>

</div>

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

{{--<script type="text/javascript" async src="https://mirabot.tenten.vn/mirabot.js?f=M6TLGi5KaHVlOdCtAgPGzgVg4zNlo0D1hggpZVc9fuOhgy29RXLRyT0eoCdaKZ33W4p2YHmXnMchLUrLpG40ni9zt1VIs7s9GX5l&t=694"></script>--}}

@yield('js')
