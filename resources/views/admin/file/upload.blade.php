<?php
/**
 * Lấy toàn bộ Meta của 1 bảng ra, và cho phép edit các meta data đó
 */
use LadLib\Common\Database\MetaTableCommon;

use Illuminate\Support\Str;
?>
@extends("layouts.adm")

@section("title")
    Upload File
@endsection

@section('header')
    @include('parts.header-all')
@endsection

@section('css')
    <link rel="stylesheet" href="/vendor/div_table2/div_table2.css?v=<?php echo filemtime(public_path().'/vendor/div_table2/div_table2.css'); ?>">
    <link rel="stylesheet" href="/admins/upload_file.css?v=<?php echo filemtime(public_path().'/admins/upload_file.css'); ?>">
@endsection

@section('js')
    <script src="/vendor/div_table2/div_table2.js?v=<?php echo filemtime(public_path().'/vendor/div_table2/div_table2.js'); ?>"></script>
    <script src="/admins/meta-data-table/meta-data-table.js"></script>
    <script src="/admins/upload_file.js?v=<?php echo filemtime(public_path().'/admins/upload_file.js'); ?>"></script>


    <script>

        clsUpload.url_server = '/api/member-file/upload';
        clsUpload.bind_selector_upload = 'drop-area-upload';
        // clsUpload.bind_selector_result = 'result-area-upload';
        clsUpload.upload_queue = 0;
        clsUpload.uploading = 0;
        clsUpload.upload_done = 0;
        clsUpload.upload_total = 0;
        clsUpload.upload_error = 0;
        clsUpload.maxFileCC = 2;
        clsUpload.mFileUpload = [];

    </script>

@endsection

@section("content")

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">
                            <a href="<?php echo request()->url() ?>">
                                UploadFile
                            </a>
                        </h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item" data-code-pos='ppp172563511451'><a href="#">Admin</a></li>
                            <li class="breadcrumb-item active"></li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">

                <?php
                \App\Models\FileUpload_Meta::includeUploadZoneHtmlSample('drop-area-upload');
                ?>


            </div>
        </section>
        <!-- /.content -->
    </div>
@endsection
