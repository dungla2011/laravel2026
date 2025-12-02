<!DOCTYPE html>
<html lang="en">
<head>
    <meta data-code-pos="ppp1678337918033" charset="utf-8">


    @php
        echo request()->all() || \LadLib\Common\UrlHelper1::getUrlRequestUri() == "/member" ?
'<meta name="viewport" content="width=device-width, initial-scale=1">' : ''
    @endphp

    {{--    <meta name="viewport" content="width=device-width, initial-scale=1">--}}

    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>
    @yield("title")
    </title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="/assert/library_ex/jquery-ui/jquery-ui.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/adminlte/plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="/assert/css/ionicons.min.css">
    <link href="/assert/css/bootstrap5.0.2.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- Theme style -->
    <link rel="stylesheet" href="/adminlte/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="/vendor/toastr/toastr.min.css">
    <link rel="stylesheet" href="/assert/css/lad-common.css">
    @yield("css")


    <style>
        .brand-link {
            border-bottom: 1px solid gray!important;
        }
    </style>

</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed1">
<div class="wrapper">
    @yield('header')
    @include("parts.nav-top")
    @include("parts.public-side-bar-lte")
    <!-- Content Wrapper. Contains page content -->
    @yield("content");
    <!-- /.content-wrapper -->
    @include("parts.footer")
    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
</div>
<!-- ./wrapper -->
<!-- jQuery -->
<script src="/adminlte/plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="/adminlte/plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>

<!-- ChartJS -->
<script src="/adminlte/plugins/chart.js/Chart.min.js"></script>
<script src="/adminlte/dist/js/adminlte.js"></script>
<script src="/admins/jctool.js?v=<?php echo filemtime(public_path().'/admins/jctool.js');?>"></script>

<script src="/vendor/toastr/toastr.min.js"></script>
<script src="/admins/toast-show.js"></script>
<script src="/admins/admin_lte_custom.js"></script>

@yield("js")
@yield("js1")




</body>
</html>
