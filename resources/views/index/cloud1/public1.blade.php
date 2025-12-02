@extends(getLayoutNameMultiReturnDefaultIfNull())


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
        .cls1 li::after {
            content: '-';
            color: transparent;
        }
    </style>

@endsection
@section('title')
    <?php
//    echo \App\Models\SiteMng::getTitle();
    ?>
    ZCloud.io.vn : File cloud management system

@endsection

@section('meta-description')<?php
    echo \App\Models\SiteMng::getDesc()
    ?>
@endsection

@section('meta-keywords')<?php
    echo \App\Models\SiteMng::getKeyword()
    ?>
@endsection

@section('content')



    <div class="container p-3 rounded text-center mt-3" style=" TEXT-ALIGN: CENTER; COLOR: royalblue; border-radius: 20px">

{{--        <img  style="width: 100%; max-width: 600px" src="/images/tmp/ev1.jpg" alt="">--}}
{{--        <img  style="width: 100%; max-width: 600px" src="/images/tmp/ev2.png" alt="">--}}

        <h2>
            FILE CLOUD MANAGEMENT SYSTEM
        </h2>
        <hr>
        <div style="display: none; margin: 0 auto; max-width: 800px; text-align: left" class="pt-2">


            <b >Tính năng</b>
            <ul class="mt-1 cls1">
                <li>Drive online gắn trực tiếp vào 1 folder Window, MacOS, Linux, Android, IOS </li>
                <li>Edit Online trên web, nhiều người cùng soạn thảo một văn bản tài liệu, với các định dạng phổ biến: Docx, Xls, Ppt, PDF</li>

                <li>Cho phép Edit tại máy khách, và ghi lên Cloud</li>
                <li>Chia sẻ public, private, chia sẻ nhóm làm việc</li>
                <li>Đăng ký online, đăng nhập, phân quyền</li>
                <li>Giới hạn Quota upload/Download</li>
                <li>Giới hạn quyền Read/write ... trên File, Folder</li>
                <li>
                    Backup linh hoạt
                </li>
                <li>
                    Dung lượng lớn với giá thấp hơn Google Driver và các dịch vụ khác, có thể ở mức 80k/1TB/Tháng
                </li>
                <li>
                    Dung lượng mở rộng dễ dàng, không giới hạn
                </li>

                <li>
                    Hỗ trợ API lập trình
                </li>

                <li>
                    Có thể customize theo yêu cầu
                </li>
            </ul>

            <b>Phù hợp với nhu cầu</b>
            <ul class="mt-1 cls1">
                <li>Nếu tổ chức của bạn muốn lưu trữ riêng, mà không dùng các dụ vụ khác GoogleDrive, OneDrive...</li>
                <li>Cần thêm bổ xung các tính năng mà các dịch vụ khác không có
                </li>
                <li>Có các dịch vụ có một số tính năng tương tự,
                    nhưng có thể không phù hợp với yêu cầu của bạn, hãy liên hệ chúng tôi để được tư vấn
                </li>

            </ul>

            <p></p>
            <b>
            Video DEMO
            </b>
            <p></p>

            <div class="txt-center">
            <iframe style="margin: 0 auto" width="560" height="315" src="https://www.youtube.com/embed/al8Zxx80sCs?si=aIYXroRLyT6vrleL" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
            </div>
            <br>
            <div class="txt-center">
                <iframe width="560" height="315" src="https://www.youtube.com/embed/CaIKddFft2k?si=YL53dy0qbt3ommEx" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>            </div>
            <br>

{{--        <img  style="width: 100%; max-width: 800px" src="/images/cloud-files.jpg" alt="">--}}



        </div>

    </div>

    <p></p>
@endsection
