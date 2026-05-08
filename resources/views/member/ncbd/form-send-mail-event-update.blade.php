{{--@extends("layouts.member")--}}
@extends("layouts_multi.ncbd")

@section("title")
    Cập nhật thông tin
@endsection

@section('header')
    @include('parts.header-all')
@endsection

@section('css')
    <link rel="stylesheet"
          href="/vendor/div_table2/div_table2.css?v=<?php echo filemtime(public_path().'/vendor/div_table2/div_table2.css'); ?>">
    <link rel="stylesheet"
          href="/admins/table_mng.css?v=<?php echo filemtime(public_path().'/admins/table_mng.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/choices.js/10.2.0/choices.min.css" />

    <?php


?>
@endsection

@section('js')
    <script src="/admins/table_mng.js"></script>
    <script src="/vendor/div_table2/div_table2.js"></script>
    <script src="/admins/meta-data-table/meta-data-table.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/choices.js/10.2.0/choices.min.js"></script>

@endsection

@section("content")

    <style>
        .member_zone i {
            color: #555555;
        }
        .img-responsive-glx {max-width: 1200px; height: auto; }
    </style>
    <?php

//    $user = \Illuminate\Support\Facades\Auth::user();

    ?>

    <div class="content-wrapper member_zone">

        <?php
        $showText = '';
        use LadLib\Common\Database\MetaClassCommon;
        use App\Models\EventUserInfo;

//$eventUserInfo = EventUserInfo::where('email', getCurrentUserEmail())->first();

        if($tmp = ($_POST['search_term'] ?? '')){

            if(str_contains($tmp, "@")){
                $type = 'email';
                $evU = EventUserInfo::where("email", $tmp)->first();
            }
            else{
                $type = 'zalo';
                $phone = \App\Models\EventUserInfo::fixPhoneNumber($tmp);
                $evU = EventUserInfo::where("phone", $phone)->first();
            }

            if(!$evU){
                $showText = ("Không tìm thấy thông tin: " . htmlspecialchars($tmp));
            }
            else{
                $evSend = new \App\Models\EventSendAction();
                $evSend->user_id = 12108;
                $evSend->type = $type;
                $evSend->event_id = 6;
                $evSend->count_send = 1;
                $evSend->pusher_chanel = 'ev000';
                $evSend->select_content = 'content4';
                $evSend->select_user_type = 'all_user';
                $evSend->user_email_send_override = "$evU->email";

                $ok = 0;
                if($check = \App\Models\EventSendAction::where('user_id' , $evSend->user_id)->where('type', $type)->where('user_email_send_override', "$evU->id")->orderBy('id','DESC')->first() ){
    //        echo "<br/>\n ----------------------- $check->created_at | " . nowyh(time() - 60);
                    if($check->created_at > nowyh(time() - 60)){
                        $showText = ("Giới hạn thời gian - Bạn hãy đợi " . (60 - (time() - strtotime($check->created_at))) . " giây để tiếp tục!");
                    }
                    else
                        $ok = 1;
                }

//                bl("OK123  ; $ok / $check / $tmp");

                if(!$check || $ok){
                    $evSend->save();
                    $showText = ("Quý khách vui lòng kiểm tra <b>$type</b> và làm theo hướng dẫn!");
                }
            }
        }
        ?>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid pt-5">
                <!-- Search Phone/Email Form -->
                <div style="text-align: center; padding: 30px 0; max-width: 800px; margin: 0 auto; background-color: #eee; border-radius: 10px;
                box-shadow : 0px 0px 5px 1px #6f6d6dab;">
                    <h3 style="margin-bottom: 30px; color: #333; ">
                        Cập nhật thông tin cá nhân
                    </h3>

                    <form method="POST" id="searchForm">
                        @csrf

                        <div class="mt-3">
                            Quý khách vui lòng nhập Email hoặc Số điện thoại đã đăng ký sự kiện, để nhận xác thực qua Email, Zalo
                        </div>

                        <div class="form-group">
                            <input
                                type="text"
                                class="form-control my-3"
                                id="searchTerm"
                                name="search_term"
                                placeholder="Nhập Email hoặc Số điện thoại"
                                style="text-align: center; font-size: 1.0rem; margin: 0 auto; max-width: 400px"
                                required>

                            {{ $showText ? tb($showText) : '' }}

                            <button type="submit" class="btn btn-primary" style="">
                                 Gửi Link Cập nhật
                            </button>

                            <div class="mt-3">
                            Với số điện thoại, Quý khách vui lòng kết bạn với số Zalo <b>0967311064</b> để nhận tin
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </section>
        <!-- /.content -->

    </div>


@endsection
