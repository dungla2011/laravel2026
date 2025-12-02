
<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        @yield('title')
    </title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <link rel="stylesheet" href="/adminlte/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

    <link rel="stylesheet" href="/adminlte/dist/css/adminlte.min.css?v=3.2.0">

    <style>
        .navbar {
            padding: 10px 10px;
        }
        .navbar-brand{
            padding-bottom: 0px;
            color: royalblue!important;
            font-weight: bold!important;
        }
        .navbar-nav > li > a {
            color: royalblue!important;
            font-weight: bold;
            font-size: 115%;
        }
        *{
            margin: 0;
            padding: 0;
            border: 0;
            font-size: 100%;
        }
        .dropdown-toggle::after {
            vertical-align: .15em;

        }

        .navbar-brand span{
            color: brown; display: inline-block; border: 0px solid brown;
            padding : 4px 2px; line-height: 80%; margin: 0px 0px;
        }
        .navbar-brand span{
            border-right: 0px solid brown;
            border-letf: 0px solid brown;
        }
        .navbar-brand span:first-child{
            /*border-left: 2px solid brown;*/
        }
        .navbar-brand span:last-child{
            /*border-right: 2px solid brown;*/
        }
        .navbar-brand span:nth-child(2){
            background-color: brown;
            color: white;
            padding: 4px;
        }


    </style>

<body class="hold-transition layout-top-nav">

<div class="wrapper">

    <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
        <div class="container">
            <button class="navbar-toggler order-1x mr-2" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <a href="/" class="navbar-brand ml-1">
{{--                <img src="/adminlte/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">--}}


<span>4</span><span>S</span><span>H</span><span>A</span><span>R</span><span>E</span>



            </a>

            <div class="collapse navbar-collapse order-3" id="navbarCollapse">

                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a href="/" class="nav-link">HOME</a>
                    </li>
                    <li class="nav-item">
                        <a href="/search-file?search_string=phim&sort_by=new&limit=10&from_size=1024&to_size=2048&ext=" class="nav-link">SEARCH</a>
                    </li>

                    <li class="nav-item dropdown">
                        <a id="dropdownSubMenu1" href="#" data-toggle="dropdown"
                           aria-haspopup="true" aria-expanded="false"
                           class="nav-link dropdown-toggle">DROP</a>
                        <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                            <li><a href="#" class="dropdown-item">Some action </a></li>
                            <li><a href="#" class="dropdown-item">Some other action</a></li>
                            <li class="dropdown-divider"></li>

                            <li class="dropdown-submenu dropdown-hover">
                                <a id="dropdownSubMenu2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle">Hover for action</a>
                                <ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">
                                    <li>
                                        <a tabindex="-1" href="#" class="dropdown-item">level 2</a>
                                    </li>

                                    <li class="dropdown-submenu">
                                        <a id="dropdownSubMenu3" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle">level 2</a>
                                        <ul aria-labelledby="dropdownSubMenu3" class="dropdown-menu border-0 shadow">
                                            <li><a href="#" class="dropdown-item">3rd level</a></li>
                                            <li><a href="#" class="dropdown-item">3rd level</a></li>
                                        </ul>
                                    </li>

                                    <li><a href="#" class="dropdown-item">level 2</a></li>
                                    <li><a href="#" class="dropdown-item">level 2</a></li>
                                </ul>
                            </li>

                        </ul>
                    </li>
                </ul>

                <form class="form-inline ml-0 ml-md-3">
                    <div class="input-group input-group-sm">
                        <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-navbar" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">

                <li class="nav-item dropdown">
                    <?php
                    if(getCurrentUserId()){
                        ?>

                    <a class="nav-link" href="/member/">
                        Member
                    </a>
                    <?php
                    }
                    else{
                    ?>
                    <a class="nav-link"  href="/login">
                        Login
                    </a>
                    <?php
                    }
                    ?>
                </li>

            </ul>
        </div>
    </nav>

<div class="wrapper">
        @yield("content")
</div>



    <footer class="main-footer">

        <div class="container">
        <div class="float-right d-none d-sm-inline">
            Anything you want
        </div>

        <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
        </div>
    </footer>

</div>



<script src="/adminlte/plugins/jquery/jquery.min.js"></script>

<script src="/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<script src="/adminlte/dist/js/adminlte.min.js?v=3.2.0"></script>
<script src="/adminlte/plugins/chart.js/Chart.min.js"></script>
<script src="/adminlte/dist/js/pages/dashboard3.js"></script>

{{--<script src="/adminlte/dist/js/demo.js"></script>--}}

</body>
</html>
