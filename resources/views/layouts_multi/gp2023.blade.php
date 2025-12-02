<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/template/glx2021/assets/img/favicon.png">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">

    <title>@yield('title')</title>


    <meta name="description" content="">
    <meta name="keywords" content="">

    <meta property="og:image" content="?v=1">
    <meta property="og:title" content="">
    <meta property="og:description" content="">

    @yield("css")


    <style>

        .auth_zone {
            margin: 0 auto;

            margin-top: 60px;
            max-width: 600px;
            /*height: 320px;*/
            border: 1px solid #ccc;
            background-color: lavender;
            border-radius: 5px;

            padding: 20px 30px;
        }
    </style>
    <style>

        .auth_zone {
            margin: 0 auto;

            margin-top: 60px;
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
    </style>

</head>

<body>
2023...
<div class="content-wrapper">
    @yield("content")
</div>

<!-- /.content-wrapper -->
<footer class="">
    <!-- /.container -->
</footer>

<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>

@yield("js")


</body>
</html>
