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
    <link rel="stylesheet"
          href="/vendor/div_table2/div_table2.css?v=<?php echo filemtime(public_path().'/vendor/div_table2/div_table2.css'); ?>">
    <link rel="stylesheet"
          href="/admins/table_mng.css?v=<?php echo filemtime(public_path().'/admins/table_mng.css'); ?>">
    <link rel="stylesheet"
          href="/admins/admin_common.css?v=<?php echo filemtime(public_path().'/admins/admin_common.css'); ?>">

    <style>
        #dailyChart, #weeklyChart, #dailyUploadChart, #dailyNewUserChart {
            width: 100%;
            height: 400px; /* Đổi theo nhu cầu */
            margin-bottom: 20px;
        }

    </style>


    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 3px 8px;
            text-align: left;
            font-size: 90%;
            background-color: white;
        }

        th {
            background-color: #f4f4f4;
        }
    </style>

@endsection

@section('js')
    <script src="/admins/table_mng.js"></script>
    <script src="/vendor/div_table2/div_table2.js"></script>
    <script src="/admins/meta-data-table/meta-data-table.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/admins/admin_logs.js"></script>

    <script>

        renderChart(<?php echo \App\Components\U4sHelper::getDownloadStats('') ?>);
        renderMonthChart(<?php echo \App\Components\U4sHelper::getMonthDownloadStats('') ?>);
        renderChartUpload(<?php echo \App\Components\U4sHelper::getUploadStats('') ?>);
        renderChartNewUsers(<?php echo \App\Components\U4sHelper::getNewUserStats('') ?>);

    </script>

@endsection

@section("content")

    <div class="content-wrapper pt-3">

        <!-- Main content -->
        <section class="content">

            <div class="container-fluid ">

                <div class="row sec1">

                    <div class="col-md-8">


                        <div class="row">


                            <div class="col-md-12">

                                <a target="_blank"
                                   href="http://v0.4share.vn/tool/bill-sum.php?tomail=1&nday=10&b_only=1"> LOG D0</a>

                                |
                                <a target="_blank" href="/tool/bill-sum.php?tomail=1&nday=10&b_only=1"> LOG D1</a>

                                |
                                <a target="_blank" href="/tool/4s/log_download_all_disk.php">Log DL All Disk</a>

                                |


                                    <a href='/tool/4s/list_uploader_big.php' target='_blank'> GET UPLODER BIG SIZE</a>


                            </div>

                        </div>


                    </div>


                </div>



                <div class="row sec1">
                    <div class="col-md-12">

                        <b data-code-pos='ppp173506161'>Biểu đồ lượt tải theo tháng (6 tháng)</b>
                        <canvas class="stat_data" id="monthChart" style="height: 240px"></canvas>

                        <b data-code-pos='ppp17353306161'>Lượt tải 90 ngày</b>
                        <canvas class="stat_data" id="dailyChart" style="height: 240px; margin-bottom: 30px"></canvas>

                        <b data-code-pos='ppp1735026161'>Upload 90 ngày</b>
                        <canvas class="stat_data" id="dailyUploadChart"
                                style="height: 240px; margin-bottom: 30px"></canvas>

                        <b data-code-pos='ppp1735026161'>New User 90 ngày</b>
                        <canvas class="stat_data" id="dailyNewUserChart"
                                style="height: 240px; margin-bottom: 30px"></canvas>
                    </div>
                </div>


                <div class="row sec1">
                    <div class="col-sm-6 mb-1" style="font-size: 100%">
                        <b data-code-pos='ppp1735046161'>LIST </b>

                        <?php

                        $str =  \App\Components\U4sHelper::getDiskInfoRemote();

                        echo $str;
                        ?>
                    </div>
                </div>
                <div class="row sec1 mb-3">
                    <div class="col-sm-6 mb-1" style="font-size: 100%">


                    </div>
                </div>


            </div>

        </section>
        <!-- /.content -->
    </div>
@endsection
