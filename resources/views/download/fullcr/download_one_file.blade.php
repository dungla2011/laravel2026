<?php




if(request('refx') == '4s')
    setcookie("refx", "4s", time() + (10 * 365 * 24 * 60 * 60), "/"); // This sets the cookie to expire in 10 years and available across the entire domain

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

    <div class="container" style="min-height: 500px">
        <div class="jumbotron" data-code-pos='ppp17126467041071'>

            <div class="rounded"
                 style="background-color: lavender; padding: 30px; margin: 0 auto; margin-top: 50px; max-width: 800px; ">



                <?php

//                    bl("Xin lỗi bạn, Dịch vụ đang bảo dưỡng, sẽ hoạt động trở lại vào 10h ngày 19.1.2025, cảm ơn bạn!");

                bl("<a href='//4share.vn/f/$fid'>Hãy Click vào đây để tải File này: https://4share.vn/f/$fid </a>",
                    "Các thanh toán của các tài khoản cũ đã được đồng bộ với 4Share.vn, cảm ơn bạn!");

                    goto _END1;

                try {

                    ?>

                    <?php
                    $idEncode = $fid;
                    $hetLuot = "";
//                    bl("FID = $fid");

                    $uid = getCurrentUserId();

                    \App\Models\OrderItem::updateCountDownloadUsed($uid);
//                    echo "\nLink Download 4Share ";


                    //        $free = OrderItem::updateArrayAndFillNumber(5, []);
                    //        return;

//                    echo "<br/>\n Tính xem đã tải được bao nhiêu lượt, còn được tải không";
//                    echo "\n UID = $uid ";

                    if (!$uid) {
                        echo("<div style='text-align: center'> Bạn cần <a href='/login' > <b> <u>ĐĂNG NHẬP</u> </b> </a>  để tải file này! </div>");
                        [$link, $fileName, $size, $fileR] = \App\Http\Controllers\DownloadController::getLinkDownload4S2($idEncode,
                            0,
                            request()->getClientIp(), 1);
                        goto _NOT_LOGIN;
                    }

                    $countCoinUsed = 0;
                    //Xem đã tải được bao nhiêu lượt, mỗi lượt sẽ có giá trị khác nhau
                    $mmDl = \App\Models\DownloadLog::where("user_id", $uid)->where('count_dl', '>', 0)->get();
                    foreach ($mmDl AS $oneDl) {
                        if ($oneDl->price_k) {
                            $countCoinUsed += $oneDl->price_k;
//                            echo "<br/>\n $oneDl->price_k";
                        } else {
                            $countCoinUsed++;
                        }
                    }
//                    $countCoinUsed = \App\Models\DownloadLog::where("user_id", $uid)->where('count_dl', '>', 0)->count();
                    //Xem đã mua gói download nào:
                    $mBill = \App\Models\OrderItem::where("user_id", $uid)->get();
//                    $ttUsed = 0;
                    $ttCoin = 0;
                    foreach ($mBill AS $oneBill) {
//                        $ttUsed += $oneBill->used;
                        $ttCoin += $oneBill->param1 * $oneBill->quantity;
                    }

                    $getFileInfoOnly = 0;
                    if ($ttCoin <= $countCoinUsed) {
                        $getFileInfoOnly = 1;
                    }

                    [$link, $fileName, $size, $fileR] = \App\Http\Controllers\DownloadController::getLinkDownload4S2($idEncode,
                        getCurrentUserId(),
                        request()->getClientIp(), $getFileInfoOnly);


                    $prCoin = 1;
                    if ($fileR->price_k) {
                        $prCoin = $fileR->price_k;
                    }

                    echo "<h6 style='text-align: center'> Cần <b style='color: red'>  $prCoin K </b> để tải file này </h6>";

                    if ($ttCoin <= $countCoinUsed) {
                        $hetLuot = ";color: #ccc;";

                        echo(" <h5 style='text-align: center'>
                        <span style='color: brown'>
                        Bạn đã hết lượt tải!
                        </span>
                        <br><br>
                        Bạn có thể mua lượt <a href='/'> <u>Tại đây</u> </a> <h5> ");

                        $link = "#";
                    } else {
                        $ttStill = $ttCoin - $countCoinUsed;
                        echo " <div style='text-align: center'> <i style='color: royalblue' class='fa fa-thumbs-up'></i>   Bạn còn  <b style='color: royalblue'> $ttStill  </b> ";
                        echo " K để sử dụng ($ttCoin - $countCoinUsed)";
                        echo " <a href='/buy-vip' style='color: blue'> Nạp thêm K</a> </div>";
                    }

                    _NOT_LOGIN:

//                        $fileName = strip_tags($fileName);
//                        $fileName = htmlspecialchars($fileName);
                    $sizeB = ByteSize($size);
                    ?>
                <div
                    style='border-radius: 10px ; background-color: white; {{$hetLuot}} padding: 20px; text-align: center; margin-top: 30px; line-height: 45px '>

                        <?php


                            if($fileR)
                        if ($uid == $fileR->user_id) {
                            echo " <a  target='_blank' href='/member/file-refer/edit/$fileR->id' style='color: blue'> [E] </a>";
                        } elseif (isAdminACP_()) {
                            echo " <a  target='_blank' href='/admin/file-refer/edit/$fileR->id' style='color: red'> [E] </a>";
                        }


                        ?>
                    Tải File

                    <br> <b style='font-size: 110%'> <a style="{{$hetLuot}} ; color: royalblue" href='{{$link}}'>

                            <img src="/images/icon/dot-blink2.gif" style="width: 30px" alt="">

                            {{$fileName}} </a> </b> <br>

                    <span style='font-size: 90%'>
    Kích thước:  <b> {{ $sizeB }} </b>
    </span>
                    <div>
                            <?php

                            echo "\n</h5>";


                            ?>

                            <?php
                        } catch (\Throwable $e) { // For PHP 7
                            bl("Error1: " . $e->getMessage());

                            if (isIPDebug()) {
                                echo "<pre>";
                                print_r($e->getTraceAsString());
                                echo "</pre>";
                            }

                        } catch (\Exception $e) {
                            bl("Error2: " . $e->getMessage());
                        }

                        _END1:
                        ?>


                    </div>

                </div>
            </div>
        </div>

@endsection

@section('title')
                Tải file {{$fileName ??''}} , {{ ($sizeB ??'') }}
@endsection
