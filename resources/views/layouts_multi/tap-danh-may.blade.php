<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="" content="">
    <meta name="description" content="@yield('description')">
    <meta name="og:description" content="@yield('description')">
    <meta name="author" content="">
    <link rel="icon" href="">

    <title>@yield('title')</title>



    <!-- Bootstrap core CSS -->
    <link href="/assert/css/bootstrap4.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:700,900" rel="stylesheet">
{{--    <link href="/assert/css/blog_sample.css" rel="stylesheet">--}}
    <link rel="stylesheet" href="/adminlte/plugins/fontawesome-free/css/all.min.css">
    <link data-pos="12133212" rel="stylesheet" type="text/css" href="/tool1/doing/typing.glx/style.5.css" />


    <style>
        *{
            margin: 0px;
            padding: 0px;
            border: 0px;
            font-size: 100%;
        }
        body{
            font-family: arial;
        }
        .search-top {
            margin-bottom: 3px;
        }
        ul, li {
            list-style: none;
            margin: 0px;
            padding: 0px;
        }
        ul {
            margin-bottom: 0px;
            padding: 5px;
        }
        li{
            margin-top: 2px;
            margin-left: 5px;
        }

        #left{
            min-height: 600px;
            border: 0px;
        }
        .blog-footer {
            color: black;
        }

        .container {
            width: 1200px;
            margin: 0 auto;
        }
    </style>


    @yield('css')


</head>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-NBND4EGTGF"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-NBND4EGTGF');
</script>

<body>

<div class="">
    <header class="blog-header py-3" style="width: 1200px; margin: 0px auto; color: #236db6!important; ">
        <div style="display: flex;  justify-content: space-between;align-items: flex-start; padding: 0px 20px ;">
            <div class="">
                <a class="text-muted" href="/" style="color: #236db6!important;">
                    <b> <i class="fa fa-home"></i> </b>
                </a>
            </div>
            <div class="">
                <a class="blog-header-logo text-dark" href="/" style="color: #236db6!important;">
                <h1 style="padding: 0px; margin: 0px">    TẬP ĐÁNH MÁY</h1>
                </a>
            </div>
            <div class="" style="">

                <?php
                if(getUserIdCurrent_()){
                    $em = explode("@", $em1 = getCurrentUserEmail())[0]."...";
                    echo "<a title='$em1' class=\"\" href=\"/member\"> $em  </a>";
                }
                else
                    echo "<a class=\"btn btn-sm btn-warning\" href=\"/login\">Login</a>";
                ?>

            </div>
        </div>
    </header>

    <div class="nav-scroller py-1 mb-2" >

        <nav class="" style="width: 1200px; font-weight: bold; margin: 0px auto;;display: flex;  justify-content: space-between;align-items: flex-start;padding:  2px; border-bottom: 1px solid #eee">
            <a class="p-2 text-muted" href="/">Trang chủ</a>
{{--            <a class="p-2 text-muted" href="/my-task">Nhiệm vụ</a>--}}
{{--            <a class="p-2 text-muted" href="/tin-tuc">Tin tức</a>--}}
            <a class="p-2 text-muted" href="/member/typing-history" data-code-pos='ppp17394962029441'>Kết quả</a>
            <a class="p-2 text-muted" href="#">Hỏi đáp</a>
            <a class="p-2 text-muted" href="#">Liên hệ</a>
        </nav>

    </div>
</div>

<main role="main" class="container" data-code-pos="ppp1682055737586" style="margin-top: 20px">

    @yield('content')

</main><!-- /.container -->

<p id="waitting_icon" style=" position:fixed;left: 50%;z-index: 1000000; display: none; top: 200px">
    <i class="fa fa-spinner fa-spin fa-2x" style="margin: 3px; background-color: snow; border-radius: 50%; color: darkslategray;">
    </i>
</p>

<div style="clear: both"></div>

<a href="https://zalo.me/0904043689" id="linkzalo" target="_blank" rel="noopener noreferrer"><div id="fcta-zalo-tracking" class="fcta-zalo-mess">
        <span id="fcta-zalo-tracking">Hỗ trợ</span></div><div class="fcta-zalo-vi-tri-nut"><div id="fcta-zalo-tracking" class="fcta-zalo-nen-nut"><div id="fcta-zalo-tracking" class="fcta-zalo-ben-trong-nut"> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 460.1 436.6"><path fill="currentColor" class="st0" d="M82.6 380.9c-1.8-.8-3.1-1.7-1-3.5 1.3-1 2.7-1.9 4.1-2.8 13.1-8.5 25.4-17.8 33.5-31.5 6.8-11.4 5.7-18.1-2.8-26.5C69 269.2 48.2 212.5 58.6 145.5 64.5 107.7 81.8 75 107 46.6c15.2-17.2 33.3-31.1 53.1-42.7 1.2-.7 2.9-.9 3.1-2.7-.4-1-1.1-.7-1.7-.7-33.7 0-67.4-.7-101 .2C28.3 1.7.5 26.6.6 62.3c.2 104.3 0 208.6 0 313 0 32.4 24.7 59.5 57 60.7 27.3 1.1 54.6.2 82 .1 2 .1 4 .2 6 .2H290c36 0 72 .2 108 0 33.4 0 60.5-27 60.5-60.3v-.6-58.5c0-1.4.5-2.9-.4-4.4-1.8.1-2.5 1.6-3.5 2.6-19.4 19.5-42.3 35.2-67.4 46.3-61.5 27.1-124.1 29-187.6 7.2-5.5-2-11.5-2.2-17.2-.8-8.4 2.1-16.7 4.6-25 7.1-24.4 7.6-49.3 11-74.8 6zm72.5-168.5c1.7-2.2 2.6-3.5 3.6-4.8 13.1-16.6 26.2-33.2 39.3-49.9 3.8-4.8 7.6-9.7 10-15.5 2.8-6.6-.2-12.8-7-15.2-3-.9-6.2-1.3-9.4-1.1-17.8-.1-35.7-.1-53.5 0-2.5 0-5 .3-7.4.9-5.6 1.4-9 7.1-7.6 12.8 1 3.8 4 6.8 7.8 7.7 2.4.6 4.9.9 7.4.8 10.8.1 21.7 0 32.5.1 1.2 0 2.7-.8 3.6 1-.9 1.2-1.8 2.4-2.7 3.5-15.5 19.6-30.9 39.3-46.4 58.9-3.8 4.9-5.8 10.3-3 16.3s8.5 7.1 14.3 7.5c4.6.3 9.3.1 14 .1 16.2 0 32.3.1 48.5-.1 8.6-.1 13.2-5.3 12.3-13.3-.7-6.3-5-9.6-13-9.7-14.1-.1-28.2 0-43.3 0zm116-52.6c-12.5-10.9-26.3-11.6-39.8-3.6-16.4 9.6-22.4 25.3-20.4 43.5 1.9 17 9.3 30.9 27.1 36.6 11.1 3.6 21.4 2.3 30.5-5.1 2.4-1.9 3.1-1.5 4.8.6 3.3 4.2 9 5.8 14 3.9 5-1.5 8.3-6.1 8.3-11.3.1-20 .2-40 0-60-.1-8-7.6-13.1-15.4-11.5-4.3.9-6.7 3.8-9.1 6.9zm69.3 37.1c-.4 25 20.3 43.9 46.3 41.3 23.9-2.4 39.4-20.3 38.6-45.6-.8-25-19.4-42.1-44.9-41.3-23.9.7-40.8 19.9-40 45.6zm-8.8-19.9c0-15.7.1-31.3 0-47 0-8-5.1-13-12.7-12.9-7.4.1-12.3 5.1-12.4 12.8-.1 4.7 0 9.3 0 14v79.5c0 6.2 3.8 11.6 8.8 12.9 6.9 1.9 14-2.2 15.8-9.1.3-1.2.5-2.4.4-3.7.2-15.5.1-31 .1-46.5z"></path></svg></div><div id="fcta-zalo-tracking" class="fcta-zalo-text">Chat ngay</div></div></div></a>
<link rel="stylesheet" href="/public/css/zalo.css">
<script>
    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) )
    {document.getElementById("linkzalo").href="https://zalo.me/0904043689";}
</script>



<footer class="blog-footer" data-code-pos='ppp17185915095391' style="width: 1200px; margin: 0 auto; text-align: center; margin-top: 20px; border-top: 1px solid #eee;  padding: 20px 0px 10px 0px">
    Phần mềm Tập đánh máy được phát triển bởi GalaxyCloud.vn
    <br>
    Miễn phí, Không chứa quảng cáo, Phần mềm có thể cá nhân hóa theo yêu cầu
    <p>
        <a href="https://galaxycloud.vn" target="_blank"> &copy GalaxyCloud.vn @ <?php
            echo date("Y")
            ?>
        </a>
    </p>
</footer>

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="/assert/js/jquery-3.6.min.js"></script>
<script src="/assert/js/popper.min.js"></script>
<script src="/assert/js/bootstrap4.min.js"></script>
<script src="/assert/js/holder.min.js"></script>


@yield('js')

</body>
</html>
