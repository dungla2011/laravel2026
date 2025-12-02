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

    <div class="container">

        <br>



        <div class="mt-4 p-4 pt-4 rounded" style="background-color: lavender; text-align: center; font-size: 120% ">
{{--            <img src="/images/icon/new_blink.gif" style="width: 80px" alt="">--}}
{{--            <br>--}}

            <b style="font-size: 130%; margin-top: 10px">

                DỊCH VỤ TẢI FILE SIÊU RẺ CHỈ TỪ 1K
            </b>
            <br>
            Tải mọi file chỉ từ 1k/1file

{{--            <br>--}}
{{--            File 100GB cũng như file 1MB... đồng giá siêu rẻ 1K!--}}
{{--            --}}
            <br>
            Gói VIP sử dụng không thời hạn, theo lượt tải file
            <br>
            Hàng triệu file sẵn sàng cho bạn tải xuống

            <p></p><p></p>
            <B>
            ĐỐI TÁC DỊCH VỤ
            </B>


            <DIV class="doiTac pt-3">
                <img src="/images/logo/logoViettel.png" alt="">
                <img src="/images/logo/logoVnpt.png" alt="">
                <img src="/images/logo/logoFpt.png" alt="">
                <img src="/images/logo/logoCmc.png" alt="">
            </DIV>

        </div>
        <div class="my-4 p-3 rounded" style="background-color: lavender; text-align: center; font-size: 130% ">
        @include('parts.buyVip')
        </div>

    </div>

@endsection
