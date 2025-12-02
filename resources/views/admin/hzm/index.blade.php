<?php

use LadLib\Common\Database\MetaClassCommon;

?>

@extends("layouts.adm")

@section("title")
    Admin Index
@endsection

@section('header')
    @include('parts.header-all')
@endsection

@section('css')
    <link rel="stylesheet" href="/vendor/div_table2/div_table2.css?v=<?php echo filemtime(public_path().'/vendor/div_table2/div_table2.css'); ?>">
    <link rel="stylesheet" href="/admins/table_mng.css?v=<?php echo filemtime(public_path().'/admins/table_mng.css'); ?>">
    <link rel="stylesheet" href="/admins/admin_common.css?v=<?php echo filemtime(public_path().'/admins/admin_common.css'); ?>">

@endsection

@section('js')
    <script src="/admins/table_mng.js"></script>
    <script src="/admins/admin_logs.js"></script>
    <script src="/vendor/div_table2/div_table2.js"></script>
    <script src="/admins/meta-data-table/meta-data-table.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>

        renderChart(<?php echo \App\Components\U4sHelper::getDownloadStats('') ?>);
        renderWeeklyChart(<?php echo \App\Components\U4sHelper::getWeeklyDownloadStats('') ?>);

        renderChartUpload(<?php echo \App\Components\U4sHelper::getUploadStats('') ?>);

        renderChartNewUsers(<?php echo \App\Components\U4sHelper::getNewUserStats('') ?>);
        renderChartNewNode(<?php echo \App\Models\GiaPha::getNewNodeStat('') ?>);

    </script>
@endsection

@section("content")

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">

                    <a href="/tool1/_site/hzm/export-shopee-xem-qua.php" > + Danh sách Sản phẩm tạm</a>

                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <br><br><br><br><br>
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="container-fluid">

                    <div class="row sec1" data-code-pos='ppp17388092187461'>
                        <div class="col-md-12">

                            <b data-code-pos='ppp1735026161'>New User 90 ngày</b>
                            <canvas class="stat_data" id="dailyNewUserChart" style="height: 240px; margin-bottom: 30px"></canvas>
                            <b data-code-pos='ppp1735026161'>Upload 90 ngày</b>
                            <canvas class="stat_data" id="dailyUploadChart" style="height: 240px; margin-bottom: 30px"></canvas>

                            <b data-code-pos='ppp17350236161'>New Node 90 ngày</b>
                            <canvas class="stat_data" id="dailyMyTreeNode" style="height: 240px; margin-bottom: 30px"></canvas>


                        </div>
                    </div>
                </div>


            </div>
        </section>
        <!-- /.content -->
    </div>
@endsection
