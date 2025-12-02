<?php
$uid = getCurrentUserId();
?>
@extends(getLayoutNameMultiReturnDefaultIfNull())

@section('title')
    {{
    \App\Models\SiteMng::getTitle()
    }}
@endsection

@section('meta-description')
    <?php
    \App\Models\SiteMng::getDesc()
    ?>
@endsection

@section('content')

    <style>
        .doiTac img {
            width: 120px;
            height: 60px;
            margin: 5px;
            border-radius: 5px;
        }
    </style>

    <div class="container mt-5">

        <br><br>
        <div class="mt-4 p-4 pt-4 rounded" style="background-color: lavender; text-align: center; font-size: 120% ">
            {{--            <img src="/images/icon/new_blink.gif" style="width: 80px" alt="">--}}
            {{--            <br>--}}

            <b style="font-size: 130%; margin-top: 10px">

                MUA GÓI TĂNG SỐ LƯỢNG THÀNH VIÊN
            </b>

            <br>
            Bạn đang có giới hạn {{ \App\Models\GiaPhaUser::getCurrentQuota($uid) }} thành viên, bạn có thể mua thêm số lượng thành viên:


            <br>


        </div>
        <div class="my-4 p-3 rounded" style="background-color: lavender; text-align: center;  " data-code-pos='ppp17395182283361'>
            <div style="font-size: 130%">
                @include('parts.buyVip')

            </div>


        </div>


{{--        <div style="text-align: center; margin: 0 auto; max-width: 800px; background-color: white" class="my-5">--}}
{{--            <div class="mb-2"> Sử dụng App Ngân hàng trên điện thoại để thanh toán. <br> Ở bước cuối cùng Bạn có thể chọn VNPay QR để quét Mã  </div>--}}
{{--            <img style="width: 100%" src="/images/chon-vnpay.png" alt="">--}}
{{--        </div>--}}

    </div>

@endsection
