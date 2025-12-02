<?php

use App\Models\OrderItem;
use App\Models\CloudServer_Meta;
use App\Models\DownloadLog;
use App\Models\TmpDownloadSession;
use App\Models\FileCloud;
use App\Models\FileRefer;
use App\Models\FileUpload;
use App\Models\Product;
use App\Models\User;
use Base\ModelCloudServer;
use Illuminate\Support\Facades\Auth;

$fid = dfh1b($ide);
$objFolder = \App\Models\FolderFile::find($fid);
$uid = getCurrentUserId();

if($objFolder)
if($objFolder->id == $objFolder->parent_id){
    $objFolder->parent_id = 0;
    $objFolder->addLog('Fix parent_id trung voi ID');
    $objFolder->save();
}

//die("111 $objFolder->id == $objFolder->parent_id");

?>


@extends(getLayoutNameMultiReturnDefaultIfNull())

@section('title')Tải Folder: {{ $objFolder->name ??'' }} @endsection

@section('css')
<style>
    .row1 {
        border-bottom: 2px solid darkorange;
    }

    .heading1 {
        background-color: darkorange;
        color: white;
        display: inline-block;
        font-weight: bold;
        padding: 7px 30px 7px 15px;
        font-size: 20px;
        text-transform: uppercase;
    }

    .heading1 a {
        color: white;
    }
    .table.table-responsive td{
        padding: 5px 20px!important;
    }
</style>
@endsection

@section('meta-description')
    <?php
    echo \App\Models\SiteMng::getDesc()
    ?>
@endsection

@section('meta-keywords')
    <?php
    echo \App\Models\SiteMng::getKeyword()
    ?>
@endsection

@section('content')

    <style>
        .folderFile a{
            color:#686868!important;
        }
    </style>
    <div class="container pt-5 mb-5" style="min-height: 500px">
        <div class="folderFile" data-code-pos='ppp17282542511' STYLE="padding: 20px; margin: 0px auto; border: 1px solid #eee; border-radius: 10px; color: #686868">
            <?php

                //GetFolderFiles pid = $fid
                $files = \App\Models\FileUpload::where('parent_id', $fid)->orderBy('name','asc')->get();
                $folders = \App\Models\FolderFile::where('parent_id', $fid)->orderBy('name','asc')->get();

                $padId = '';
                if(isSupperAdmin_()){
                    $padId = "<a style='color: blue!important' target='_blank' href='/admin/folder-file/edit/$fid'> $fid</a>";
                }
//
//                //List ra folder
//                foreach ($folders as $folder) {
//                    echo '<div class="">';
//                    echo '<div class=""><a href="/d/$folder->link1">' . $folder->name . '</a> </div>';
//                    echo '</div>';
//                }
//
//                $cc = 0;
//                foreach ($files as $file) {
//                    $cc++;
//                    $sizeB = ByteSize($file->size);
//                    echo '<div class="">';
//                    echo "<div class=''><a href='/d/$file->link1'>$cc. $sizeB </a> </div>";
//                    echo "<div class=''><a href='/d/$file->link1'> $file->name </a> </div>";
//                    echo '</div>';
//                }
?>
            <?php

            if($objFolder){
                ?>
                <h2 class=""> {{$objFolder->name}}  <?php echo $padId ?> </h2>
            <?php
            }

            $cc = 1;
            if(count($folders)){
            ?>

            <table class="table table-responsive" style="border: 1px solid #ddd;">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Folder Name</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($folders as $folder)

                    <?php

                        if($folder->id == $folder->parent_id){
                            $folder->parent_id = 0;
                            $folder->addLog('Fix parent_id trung voi ID');
                            $folder->save();
                        }
                    ?>

                    <tr>
                        <td style="width: 60px">{{ $cc++ }}</td>
                        <td><a href="/d/{{ $folder->link1 }}"> <b> <i class="fa fa-folder"></i>  {{ $folder->name }} </b></a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <?php
            }
            ?>

            <table class="table table-responsive">
                <thead>
                <tr>
                    <th>#</th>
                    <th>File Size</th>
                    <th>File Name</th>
                </tr>
                </thead>
                <tbody>
                @php $cc = 1; @endphp
                @foreach ($files as $file)
                    <tr>
                        <td style="width: 60px">{{ $cc++ }}</td>
                        <td>{{ ByteSize($file->file_size) }}</td>
                        <td><a class="link_file" href="/f/{{ $file->link1 }}"><i class="fa fa-file"></i>  {{ $file->name }}</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <?php


            ?>





        </div>

        <button class="btn btn-primary" onclick="copyAllLink()">Copy All Link</button>


    </div>

@endsection

