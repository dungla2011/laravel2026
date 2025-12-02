<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="">

    <title>@yield('title')</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.0/examples/blog/">

    <!-- Bootstrap core CSS -->
    <link href="/assert/css/bootstrap4.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:700,900" rel="stylesheet">
    <link href="/assert/css/blog_sample.css" rel="stylesheet">

    <style>
        .nav-scroller a {
            color: #236db6!important;
            text-decoration: none;
        }
        .nav-scroller a:hover {
            font-style: italic;
        }


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
        .alert-danger {
            color: #721c24;
            background-color: transparent!important;
            border-color: transparent!important;
            color: red;
            font-style: italic;
            /* font-size: small; */
        }

        .alert {
            position: relative;
            padding: 3px 3px!important;;
            margin-bottom: 1rem!important;;
            margin-top: 1px!important;
            /* border-radius: 0.25rem; */
        }
        .content-wrapper {
            min-height: 600px;
        }

        .navbar-dark .navbar-nav .nav-link {
            color: white!important;
        }
    </style>



    @yield('css')
</head>

<body>

<div class="container">
    <header class="blog-header py-3" style="color: #236db6!important;">
        <div class="row flex-nowrap justify-content-between align-items-center">
            <div class="col-4 pt-1">
                <a class="text-muted" href="/" style="color: #236db6!important;">In-GOD-We-Trust</a>
            </div>
            <div class="col-4 text-center">
                <a class="blog-header-logo text-dark" href="/" style="color: #236db6!important;">A105</a>
            </div>
            <div class="col-4 d-flex justify-content-end align-items-center">
                <a class="text-muted" href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-3"><circle cx="10.5" cy="10.5" r="7.5"></circle><line x1="21" y1="21" x2="15.8" y2="15.8"></line></svg>
                </a>
                <?php
                if(getUserIdCurrent_())
                    echo "<a class=\"btn btn-sm btn-outline-secondary\" href=\"/member\">Thành viên</a>";
                else
                    echo "<a class=\"btn btn-sm btn-outline-secondary\" href=\"/login\">Login</a>";
                ?>

            </div>
        </div>
    </header>

    <div class="nav-scroller py-1 mb-2" style="color: #236db6!important;">
        <nav class="nav d-flex justify-content-between">
            <a class="p-2 text-muted" href="#">Trang chủ</a>
            <a class="p-2 text-muted" href="#">Sản phẩm</a>
            <a class="p-2 text-muted" href="#">Dịch vụ</a>
            <a class="p-2 text-muted" href="#">Tin tức</a>
            <a class="p-2 text-muted" href="#">Giới thiệu</a>
            <a class="p-2 text-muted" href="#">Hỏi đáp</a>
            <a class="p-2 text-muted" href="#">Liên hệ</a>
        </nav>
    </div>
</div>

<main role="main" class="container">
    <div class="row">
        @yield('content')
    </div><!-- /.row -->

</main><!-- /.container -->

<footer class="blog-footer">

    <p>
        <a href="#">Back to top</a>
    </p>
</footer>

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="/assert/js/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="/assert/js/popper.min.js"></script>
<script src="/assert/js/bootstrap4.min.js"></script>
<script src="/assert/js/holder.min.js"></script>
</body>
</html>
