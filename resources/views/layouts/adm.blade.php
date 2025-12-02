<!DOCTYPE html>
<html lang="en">
<head>

    <meta data-code-pos="ppp1678337934479" charset="utf-8">

    @php
        echo request()->all() || \LadLib\Common\UrlHelper1::getUrlRequestUri() == "/admin" ?
'<meta name="viewport" content="width=device-width, initial-scale=1">' : ''
    @endphp


    {{--    <title>AdminLTE 3 | Dashboard</title>--}}
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <title>
        @yield("title")
    </title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    {{--    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"--}}
    {{--          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">--}}

    <!-- Font Awesome -->
    <link rel="stylesheet" href="/adminlte/plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="/assert/css/ionicons.min.css">
    <link href="/assert/css/bootstrap5.0.2.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link rel="stylesheet" href="/assert/library_ex/jquery-ui/jquery-ui.css">
    <!-- Tempusdominus Bootstrap 4 -->
    {{--    <link rel="stylesheet" href="/adminlte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">--}}
    <!-- iCheck -->
    {{--    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">--}}

    {{--    <link rel="stylesheet" href="/adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css">--}}
    <!-- JQVMap -->
    {{--    <link rel="stylesheet" href="/adminlte/plugins/jqvmap/jqvmap.min.css">--}}
    <!-- Theme style -->
    <link rel="stylesheet" href="/adminlte/dist/css/adminlte.min.css">
    <!-- overlayScrollbars -->
    {{--    <link rel="stylesheet" href="/adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">--}}
    <!-- Daterange picker -->
    {{--    <link rel="stylesheet" href="/adminlte/plugins/daterangepicker/daterangepicker.css">--}}
    <!-- summernote -->
    {{--    <link rel="stylesheet" href="/adminlte/plugins/summernote/summernote-bs4.min.css">--}}
    <link rel="stylesheet" href="/vendor/toastr/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css">

    <link rel="stylesheet"
          href="/assert/css/lad-common.css?v=<?php echo filemtime(public_path("/assert/css/lad-common.css")) ?>">



    @yield("css")


</head>

<body data-code-pos='ppp17326752951' class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed
<?php
if(\App\Models\SiteMng::isAdminSidebarCollapse())
    echo "sidebar-collapse";
?>
">


<div class="wrapper">
    @yield('header')
    @include("parts.nav-top")
    @include("parts.side-bar")
    <!-- Content Wrapper. Contains page content -->
    @yield("content")
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

<script src="/assert/js/bootstrap.5.bundle.min.js"></script>

<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>

<!-- ChartJS -->
<script src="/adminlte/plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
{{--<script src="/adminlte/plugins/sparklines/sparkline.js"></script>--}}
{{--<!-- JQVMap -->--}}
{{--<script src="/adminlte/plugins/jqvmap/jquery.vmap.min.js"></script>--}}
{{--<script src="/adminlte/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>--}}
{{--<!-- jQuery Knob Chart -->--}}
{{--<script src="/adminlte/plugins/jquery-knob/jquery.knob.min.js"></script>--}}
{{--<!-- daterangepicker -->--}}
{{--<script src="/adminlte/plugins/moment/moment.min.js"></script>--}}
{{--<script src="/adminlte/plugins/daterangepicker/daterangepicker.js"></script>--}}
<!-- Tempusdominus Bootstrap 4 -->
{{--<script src="/adminlte/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>--}}



<!-- Summernote -->
{{--<script src="/adminlte/plugins/summernote/summernote-bs4.min.js"></script>--}}
<!-- overlayScrollbars -->
{{--<script src="/adminlte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>--}}
<!-- AdminLTE App -->
<script src="/adminlte/dist/js/adminlte.js"></script>
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<!-- AdminLTE for demo purposes -->
{{--<script src="/adminlte/dist/js/demo.js"></script>--}}
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
{{--<script src="/adminlte/dist/js/pages/dashboard.js"></script>--}}
<script src="/admins/jctool.js?v=<?php echo filemtime(public_path().'/admins/jctool.js');?>"></script>

<script src="/vendor/toastr/toastr.min.js"></script>
<script src="/admins/toast-show.js"></script>
<script src="/admins/admin_lte_custom.js"></script>

@yield("js")
@yield("js1")


<p id="waitting_icon" style=" position:fixed;left: 50%;z-index: 1000000; display: none; top: 200px">
    <i class="fa fa-spinner fa-spin fa-2x"
       style="margin: 3px; background-color: snow; border-radius: 50%; color: darkslategray;">
    </i>
</p>
</body>
</html>
