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
$obj = FileUpload::find($fid);

?>
@extends(getLayoutNameMultiReturnDefaultIfNull())

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
</style>


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




    <div class="container pt-3 " style="min-height: 500px">

        <?php
//        echo "<br/>\n $ide";

        $uid = getCurrentUserId();
        if(!$uid){
            echo "<br/>\n Chưa đăng nhập";
            goto _END;
        }

        $obj = FileUpload::find($fid);

        if(!$obj){
            bl("File không tồn tại hoặc đã bị xoá");
            return;
//    goto _END;
        }

            try{
                //Kiểm tra download allow:
                $mm = TmpDownloadSession::getLinkDownload4s($ide, $uid);
            }
            catch (Exception $exception){
                bl("Some error: " . $exception->getMessage());
                if(isDevEmail()){
                   bl("Error: " . $exception->getTraceAsString());
                }
                goto _END;
            }
//$mm['sid']
//$mm['done_bytes']
        $link = $mm['dlink'];

//        echo "\n $link";
        ?>

        <div class="jumbotron jumbotron-fluid bg-light py-3 mt-3" data-code-pos='ppp17282972542511'>
            <div class="container">

                <?php
                //Kiem tra xem file Remote co ton tai khong

                ?>

                <b class="text-center pt-3" data-code-pos='ppp17282972582511'>
                    {{ substr($obj->name,0,100) . "..." }}
                </b>
                <div class="m-4" data-code-pos='ppp17282972605731'>
                <a href='{{$link}}'><button class='btn btn-primary txt-light'> Download</button></a>
                </div>
                </div>
        </div>

        <?php
        _END:
        ?>
    </div>

@endsection

@section('title')
    {{ $obj->name ??'' }} , {{ ($sizeB ??'') }}
@endsection
