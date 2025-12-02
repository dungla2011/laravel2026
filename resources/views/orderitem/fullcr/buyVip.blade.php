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

        <div class="mt-4 p-4 pt-4 rounded" style="background-color: lavender; text-align: center; font-size: 120% ">
            {{--            <img src="/images/icon/new_blink.gif" style="width: 80px" alt="">--}}
            {{--            <br>--}}

            <b style="font-size: 130%; margin-top: 10px">

                MUA GÓI TẢI FILE
            </b>

            <br>
{{--            Bạn đang có giới hạn {{ \App\Models\GiaPhaUser::getCurrentQuota($uid) }} thành viên, bạn có thể mua thêm số lượng thành viên:--}}


            <br>


        </div>
        <div class="my-4 p-3 rounded" style="background-color: lavender; text-align: center; font-size: 130% " data-code-pos='ppp17395182451151'>
{{--            @include('parts.buyVip')--}}
        </div>

    </div>

@endsection
