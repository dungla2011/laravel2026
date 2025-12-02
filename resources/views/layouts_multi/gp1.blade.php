<?php

$uiTopBn = \App\Models\BlockUi::getOneSName_Static('top_banner');
$mimg = $uiTopBn->getAllImageList();
$img0 = '/template/gp1/images/background-banner2.png';
if(isset($mimg[1]))
    $img0 = $mimg[1];
?>
<!DOCTYPE html>
<html lang="vi" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=5, user-scalable=yes">

    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-title" content="@yield("title")"/>

    <title>
        @yield("title")
    </title>

    <meta name="description" content="@yield("description")">
    <meta name="keywords" content="">
    <meta name="author" content="galaxycloud.vn">
    <meta name="copyright" content="galaxycloud.vn"/>
    <meta name="robots" content="index,follow"/>

    <meta property="og:image" content="@yield('logo')">

    <meta property="og:title" content="@yield('title')">
    <meta property="og:description" content="@yield('description')">

    <link rel="stylesheet" href="https://demo-tintuc.galaxycloud.vn/public/css/lad-common.css">

    <link type="image/x-icon" href="@yield('logo')" rel="shortcut icon"/>
    <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed:400,500,700&amp;subset=vietnamese"
          rel="stylesheet">
    <link rel="stylesheet" href="/assert/font-awesome/font-awesome.min.css" media="all"/>
    <link rel="stylesheet" href="https://demo-tintuc.galaxycloud.vn/public/css/bootstrap.min.css" media="all"/>

    <link rel="stylesheet" href="/template/gp1/css/style.css" media="all"/>

    <!--Jquery lib-->
    <script src="https://demo-tintuc.galaxycloud.vn/public/js/jquery-2.1.4.min.js"></script>

    <style>
        .img-responsive-glx, .img_in_news {
            max-width: 100% !important;
        }
        #logo-bar {
            background-size: cover;
            padding-bottom: 5px;
        }
        .qqqq1111 {

        }
        .block_scoll_menu #auto_footer_menu .list_item_panel li .level2 a, .block_scoll_menu #auto_footer_menu .list_item_panel li .level3 a {
            color: brown;
        }

        .block_scoll_menu #auto_footer_menu .list_item_panel li .level2, .block_scoll_menu #auto_footer_menu .list_item_panel li .level3 {
            background-color: transparent;
        }

        .nav-bar-pc .site-menu > li .level2 li .level3 {
            background-color: darkred;
        }

        .nav-bar-pc .site-menu > li .level2 {
            background-color: darkred;
        }

        .nav-bar-pc .site-menu > li .level2 li a:hover {
            background-color: brown;
        }

        .nav-bar-pc .site-menu > li .level3 li a:hover {
            background-color: brown;
        }

        .cat-head h2 a {
            color: brown;
        }

        .nav-bar-pc {
            background: brown;
        }

        /* Chrome, Safari, Opera */
        @-webkit-keyframes xoayvong {
            from {
                -webkit-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -o-transform: rotate(0deg);
            }
            to {
                -webkit-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -o-transform: rotate(360deg);
            }
        }

        /* Standard syntax */
        @keyframes xoayvong {
            from {
                -webkit-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -o-transform: rotate(0deg);
            }
            to {
                -webkit-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -o-transform: rotate(360deg);
            }
        }


        .lgxoay {
            width: 160px;
            height: 160px;
            /*background: url(/template/gp1/images/logo_xoay.png);*/
            /*background-size: cover;*/
            display: inline-block;
        }

        @-webkit-keyframes rotating {
            from {
                -webkit-transform: rotate(0deg);
            }
            to {
                -webkit-transform: rotate(360deg);
            }
        }

        .rotating {
            -webkit-animation: rotating 10s linear infinite;
        }
    </style>

    <?php
    \App\Models\BlockUi::showCssHoverBlock();
    ?>

    @yield("css")

</head>


<body class="body_home">

<div data-code-pos="ppp1681472461705">
    <?php
    echo \App\Models\SiteMng::getGoogleAnalyticCode();
    ?>
</div>

<?php



$pr = ['pid' => 3, 'get_all' => 1, 'order_by' => 'orders', 'order_type' => 'ASC'];
$obj = new \App\Models\MenuTree();
$ret = $obj->queryIndexTree($pr, new \App\Components\clsParamRequestEx());
if(!$ret){
    return;
}

$gid = 3;

//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($ret[0]);
//        echo "</pre>";

$mData = [];

foreach ($ret[0] AS $m1) {
    if (!$m1['gid_allow'])
        continue;
    $std = new stdClass();
    $std->id = $m1['id'];
    $std->name = $m1['name'];
    $std->link = $m1['link'];
    $std->icon = $m1['icon'];
    $std->parent_id = $m1['parent_id'];
    $std->disable_href = 0;
    $mData[] = $std;
    //
}
//
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($mData);
//        echo "</pre>";

$mData1 = unserialize(serialize($mData));
$retMenu = \App\Components\MenuHelper1::show_menu_demo1_recursive($mData1, 3);
$mData1 = unserialize(serialize($mData));
//
$retMenuMobi = \App\Components\MenuHelper1::show_menu_demo1_recursive_mobi($mData1, 3);

?>

<!--HEADER MOBILE-->
<header id="header" class="section header">

    <a class="img_logo" href="/" style="font-size: 20px; color: darkred; display: block; margin-top: 5px">

        <?php

        //        \App\Models\BlockUi::showEditLink_("abc" , 'sửa khối abc');
        if($uiTopBn && $uiTopBn->status){
            $mimg = $uiTopBn->getAllImageList();
            if(isset($mimg[1])){
                $img0 = $mimg[1];
                ?>


            <div data-code-pos='ppp16900999250751' class="" style="margin: 0 auto; text-align: center; display: flex; justify-content: center; align-items: center; width: 290px; height: 50px; background-image: url('<?php echo $img0 ?>');  background-repeat: no-repeat; background-size: 100% 100%;">
                <div style="text-align: center">
                    <?php
                    echo $uiTopBn->getSummary2();
                    ?>
                </div>
            </div>
        </div>
        <?php

            }
        }

        ?>


        {{--        <img class="logo_web" src="/adminlte/dist/img/AdminLTELogo.png" alt="Trang chủ" style="" data-was-processed="true">--}}
    </a>
    <div class="btn_control_menu"><i class="fa fa-bars"></i></div>
    <button data-code-pos='ppp17371659155071' id="auto_search_button" type="submit" value=""><i class="fa fa-search"></i></button>
    <div class="block_search section">
        <form id="search" action="https://timkiem.MSN.net/" method="get">
            <div class="warp">
                <input id="auto_search_textbox" maxlength="80" name="q" class="input_form" placeholder="Tìm kiếm">
                <button type="submit" id="btn_search_ns" class="btn_search"><i class="fa fa-search"></i></button>
                <button type="reset" class="btn_reset">×</button>
            </div>
        </form>
    </div>
</header>
<!--END HEADER MOBILE-->
<!-- TOP BAR -->
<section id="top-bar" class="clearfix qqqq1111">
    <?php
    $ui = \App\Models\BlockUi::showEditButtonStatic('top_line_trang_chu');
    ?>
    <div class="container">
        <span class="datetime"><i class="fa fa-clock-o"></i>
            <?php
            echo \LadLib\Common\clsDateTime2::showDateTimeStringVNToDisplay(time());
            ?>
        </span>

        <?php
        if($ui && $ui->status){
            echo $ui->summary;
        }
        else{
            echo "<style> #top-bar { display: none} </style>";
        }
        ?>
{{--        <span class="hotline"><i class="fa fa-phone"></i>Phòng Truyền thông:  0968686868  </span>--}}
{{--        <a href="#" class="ads-contact"><i class="fa fa-envelope-o"></i>Liên hệ quảng cáo</a>--}}

        <form id="frmSearch" name="frmSearch">
            <input type="text" onkeypress="return enter_search_q(event);" name="q" value="" placeholder="Tìm kiếm">
            <input type="submit" name="submit" value="" onclick="return submit_search_q();">
        </form>
    </div>
</section>
<!--END TOP BAR -->
<?php
$bg = '/template/gp1/images/background-banner.png';
if($ui = \App\Models\BlockUi::getOneSName_Static('top_banner_background')){
    $bg = $ui->getThumbInImageList();
}
?>
<section id="logo-bar" class="clearfix qqqq1111" style="background-image: url(<?php echo $bg?>); background-size: 100% 100%">
    <?php
    $ui = \App\Models\BlockUi::showEditButtonStatic('top_banner_background');
    ?>
    <div class="container qqqq1111 zzz21" >
        <?php
        $ui = \App\Models\BlockUi::showEditButtonStatic('top_banner');
//        \App\Models\BlockUi::showEditLink_("abc" , 'sửa khối abc');
        if($ui && $ui->status){
            $mimg = $ui->getAllImageList();
            $img1= '';$img0= '';
            if(isset($mimg[0]))
                $img0 = $mimg[0];
            if(isset($mimg[1]))
                $img1 = $mimg[1];
        ?>
        {{--        <img class="lgxoay" src="/template/gp1/images/logo_xoay.png" alt="">--}}
        <div style="position: relative; display: inline-block; padding-top: 10px">
            <div class="rotating lgxoay" style="background: url(<?php echo $img0 ?>);
            background-size: cover;">
            </div>
            <a href="/">
                <div style="position: absolute; top: 62px; left: 12%; ">
                    <div style="text-align: center; font-size: 22px; color: darkred">

                            <?php
                            echo $ui->getSummary();
                            ?>

                    </div>
                </div>
            </a>
        </div>
        <div class="banner-top hide-mobile" style="text-align: center; display: flex; justify-content: center; align-items: center; width: 720px; height: 150px; background-image: url('<?php echo $img1 ?>');  background-repeat: no-repeat; background-size: 100% 100%;">
            <div style="text-align: center">
            <?php
            echo $ui->getContent();
            ?>
            </div>
        </div>
        <?php
        }else{
                echo "<style> #logo-bar { display: none } </style>";
        }
        ?>
    </div>
</section>
<!--MENU MANIN PC-->
<section id="nav-bar" class="nav-bar-pc clearfix">
    <div class="container qqqq1111">

        <?php
        \App\Models\BlockUi::showEditLink_("/admin/menu-tree/tree?pid=3&gid=1&open_all=1",' Edit menu ');
        ?>
        <ul class="site-menu" style="">

            <li class="home">
                <a href="/" class="menu-link"><i class="fa fa-home"></i></a>
            </li>
            <?php
            echo $retMenu;
            ?>

            <li class="home">
                <a href="/login" class="menu-link"><i class="fa fa-user"></i>
                    <?php
                    if(getCurrentUserId()){
                        echo "Tài khoản";
                    }
                    else
                        echo "Login";
                    ?>

                </a>
            </li>

        </ul>


    </div>
</section>

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

    /*Slide text*/
    .wrapper1 {
        max-width: 100%;
        overflow: hidden;
    }

    .marquee1 {
        white-space: nowrap;
        overflow: hidden;
        display: inline-block;
        animation: marquee1 15s linear infinite;
    }

    .marquee1 p {
        margin: 0px;
        display: inline-block !important;
        color: red;
        font-weight: bold;
    }

    @keyframes marquee1 {
        0% {
            transform: translate3d(0, 0, 0);
        }
        100% {
            transform: translate3d(-50%, 0, 0);
        }
    }

</style>

<!--MENU MANIN PC-->
<section id="line_top_slide" class="" style="padding: 15px 1px 8px 1px">
    <div class="container qqqq1111" >
        <?php
        $ui = \App\Models\BlockUi::showEditButtonStatic('line_top_slide');

        ?>
        <div class="wrapper1">
            <div class="marquee1 ">
                <?php
                if($ui){
                    echo $ui->getSummary();
                }
                ?>
            </div>
        </div>
    </div>
</section>

<!-- FileView=/application/module/news/index-view/demo-tintuc.galaxycloud.vn/index.phtml -->
<!--<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">-->


@yield("content")


<script>

    $("#sort_by_id").click(function () {

        $("#modalSortBy").modal("toggle");

    })

    $("[id^='id_dropbtn_glx_']").click(function () {
        //$(".dropdown_glx-content").toggle();
    })

</script>

<div class="clearfix"></div>

<!-- FOOTER -->
<footer id="footer">
    <!--MENU MANIN PC-->

    <section class="nav-bar-pc clearfix">
        <div class="container">
            <ul class="site-menu" style="">
                <li class="home">
                    <a href="/" class="menu-link"><i class="fa fa-home"></i></a>
                </li>

                <?php

                echo $retMenu;

                ?>

            </ul>
        </div>
    </section>
    <!--MENU MANIN PC-->


    <div class="container">
        <div class="footer-info">

            <div class="left" id='editable_glx_3'>

                <div class="box-contact clearfix qqqq1111">
                    <?php
                    $ui = \App\Models\BlockUi::showEditButtonStatic('hot_line_bottom');
                    if($ui)
                        echo $ui->getSummary();
                    ?>

                </div>

                <div class="box-social qqqq1111">
                    <?php
                    $ui = \App\Models\BlockUi::showEditButtonStatic('social_bottom');
                    if($ui)
                        echo $ui->getSummary();
                    ?>

                </div>

                <div class="site-info qqqq1111">

                    <?php
                    $ui = \App\Models\BlockUi::showEditButtonStatic('info_line_bottom');
                    if($ui)
                        echo $ui->getSummary();
                    ?>


                </div>
            </div>
            <div class="right">
                <a href="" class="dmca"><img
                        src="https://demo-tintuc.galaxycloud.vn/template/demo-tintuc.galaxycloud.vn/images/graphics/dmca.png"
                        alt=""></a>
            </div>
        </div>

    </div>
</footer>
<!--END FOOTER -->
<div class="social_fixed">
    <a href="#" target="_blank" class="fb"><i class="fa fa-facebook"></i><span>3.1k</span></a>
    <a href="#" target="_blank" class="tw"><i class="fa fa-twitter"></i></a>
    <a href="#" target="_blank" class="gp"><i class="fa fa-google"></i><span>1.6k</span></a>
</div>
<div class="mask-content"></div>
<!--MAIN MENU-->
<nav id="main_menu" class="main_menu">
    <div class="header_menu section">
        <span id="auto_close_left_menu_button" class="close_main_menu">×</span>
        <div class="my_contact">
{{--            <p><i class="fa fa-phone"></i><a style="color:#206dc0;font-weight:bold">0999 999 999</a></p>--}}
{{--            <p class="email" style="">--}}
{{--                <a style=""><i class="fa fa-envelope-o"></i>support@gmail.com</a>--}}
{{--            </p>--}}
        </div>
    </div>
    <div class="block_scoll_menu section">
        <div class="block_search section">
            <form id="search" action="" method="get">
                <div class="warp">
                    <input id="auto_search_textbox" maxlength="80" name="q" class="input_form" placeholder="Tìm kiếm"
                           type="search">
                    <button type="submit" id="btn_search_ns" class="btn_search"><i class="fa fa-search"></i></button>
                    <button type="reset" class="btn_reset">×</button>
                </div>
            </form>
        </div>
        <div class="list_menu section" id="auto_footer_menu">
            <ul class="list_item_panel section" id="auto_footer_first_list">

                <?php
                echo "" . $retMenuMobi
                ?>

            </ul>
        </div>

    </div>
</nav>


<!--END MAIN MENU-->
<a href="javascript:;" id="to_top"><i class="fa fa-long-arrow-up"></i></a>


<script src="https://demo-tintuc.galaxycloud.vn/template/demo-tintuc.galaxycloud.vn/js/owl.carousel.min.js"></script>
<script src="https://demo-tintuc.galaxycloud.vn/public/js/bootstrap.min.js"></script>
<script
    src="https://demo-tintuc.galaxycloud.vn/template/demo-tintuc.galaxycloud.vn/js/jquery.scrollbar.min.js"></script>
<script src="https://demo-tintuc.galaxycloud.vn/template/demo-tintuc.galaxycloud.vn/js/common.js"></script>
<!--Owl slider lib-->

<script src="https://demo-tintuc.galaxycloud.vn/application/_js/lib_base.js?v=1673260524"></script>
<script>
    $(document).ready(function () {
        jQuery('.scrollbar-inner').scrollbar();
    });
</script>


<!-- Trigger the modal with a button -->

<!--<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">TestDialog</button>-->

<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Modal Header</h4>
            </div>
            <div class="modal-body">
                <p>Some text in the modal.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>


</body>
</html>

<!-- LOADTIME1 = 1.4340 (ActionTime: 0.0919) , 222.254.10.203

 *** This website is Powered By GalaxyCloud.vn ***
 -->
