<?php

use LadLib\Common\Database\MetaClassCommon;

$layout = $isMember ?? '' ? "layouts.member" : "layouts.adm";

$admin = "admin";



if ($isMember ?? '')
    $admin = "member";


$metaEventAndUser = \App\Models\EventAndUser::getMetaObj();
//$metaEvent = $metaEvent->getMetaDataApi();

$dep_id = request('dep_id');


//$mev sắp xếp lại mảng này gồm 2 mảng ghep lại
// mảng 1 lấy ra tất cả sk có time_start sau hiện tại sắp xếp theo thời gian tăng dần
// mảng 2 lấy ra tất cả sk có time_start trước hiện tại sắp xếp theo thời gian giảm dần
// mảng 1 + mảng 2 = mảng mới
function sortAndMergeEvents($events) {
    $futureEvents = [];
    $pastEvents = [];
    $now = new DateTime();

    foreach ($events as $event) {
        $eventStartTime = new DateTime($event->time_start);
        if ($eventStartTime > $now) {
            $futureEvents[] = $event;
        } else {
            $pastEvents[] = $event;
        }
    }

    usort($futureEvents, function($a, $b) {
        return new DateTime($a->time_start) <=> new DateTime($b->time_start);
    });

    usort($pastEvents, function($a, $b) {
        return new DateTime($b->time_start) <=> new DateTime($a->time_start);
    });

    return array_merge($futureEvents, $pastEvents);
}

?>
@extends($layout)

@section("title")
    Admin Index
@endsection

@section('title_nav_bar')
    ADMIN  EVENTs DASHBOARD
@endsection

@section('header')
    @include('parts.header-all')
@endsection

@section('css')
    <link rel="stylesheet"
          href="/vendor/div_table2/div_table2.css?v=<?php echo filemtime(public_path().'/vendor/div_table2/div_table2.css'); ?>">
    <link rel="stylesheet"
          href="/admins/table_mng.css?v=<?php echo filemtime(public_path().'/admins/table_mng.css'); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Outlined"
          rel="stylesheet">
@endsection

@section('js')
    <script src="/admins/table_mng.js"></script>
    <script src="/vendor/div_table2/div_table2.js"></script>
    <script src="/admins/meta-data-table/meta-data-table.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@endsection

@section("content")

    <script>

        class CountdownTimer {
            constructor(startDate, endDate, elementId) {
                this.startDate = new Date(startDate);
                this.endDate = new Date(endDate);
                this.elementId = elementId;
                this.intervalId = null;
            }

            start() {
                this.update();
                this.intervalId = setInterval(() => this.update(), 1000);
            }

            formatTime(value) {
                return value.toString().padStart(2, '0');
            }

            update() {
                const now = new Date();
                const element = document.getElementById(this.elementId);

                // Kiểm tra nếu sự kiện chưa bắt đầu
                if (now < this.startDate) {
                    const timeDifference = this.startDate - now;
                    const days = Math.floor(timeDifference / (1000 * 60 * 60 * 24));
                    const hours = this.formatTime(Math.floor((timeDifference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)));
                    const minutes = this.formatTime(Math.floor((timeDifference % (1000 * 60 * 60)) / (1000 * 60)));
                    const seconds = this.formatTime(Math.floor((timeDifference % (1000 * 60)) / 1000));

                    element.innerHTML = `Còn <span class="badge bg-primary" style="font-size: 100%; float: none; overflow: initial">${days} </span> ngày ${hours}:${minutes}:${seconds}`;
                    element.className = 'count_down';

                    if(days <= 2){
                        element.style.backgroundColor = '#17a2b8';
                    }
                }
                // Kiểm tra nếu sự kiện đang diễn ra
                else if (now >= this.startDate && now < this.endDate) {
                    element.innerHTML = 'Đang diễn ra';
                    element.className = 'count_down ongoing';
                    element.style.backgroundColor = '#28a745'; // Màu xanh lá
                }
                // Sự kiện đã kết thúc
                else {
                    element.innerHTML = 'Đã kết thúc';
                    element.className = 'count_down red_color1';
                    clearInterval(this.intervalId);
                }
            }
        }


    </script>

    <style>
        .count_down_p{
            padding-top: 0px;
            padding-bottom: 0px;
        }
        .count_down {
            padding: 3px 8px ;
            font-size: 80%!important;
            border-radius: 5px;
            background-color: #17a2b8;
            color: white!important;
            /*font-style: italic;*/
        }
        .red_color1 {
            background-color: #aaa;
        }
        .ongoing {
            background-color: orange !important;
            color: white !important;
        }
        .text_cut {

            white-space: nowrap; /* Không cho xuống dòng */
            overflow: hidden; /* Ẩn phần tràn ra ngoài */
            text-overflow: ellipsis; /* Hiển thị dấu ba chấm */
            /*width: 200px;               !* Độ rộng tối đa cho phần tử *!*/
        }

        .text_cut span {
            display: inline-block; /* Ensure the span is treated as a block element */
            max-width: 100%; /* Ensure the span does not exceed the width of the parent */
            white-space: nowrap; /* Không cho xuống dòng */
            overflow: hidden; /* Ẩn phần tràn ra ngoài */
            text-overflow: ellipsis; /* Hiển thị dấu ba chấm */
        }

        .nav-link span {
            margin-top: 3px;
            float: right;
            font-size: 90%;
            color: black;
        }

    </style>

    <?php

//        dump($isMember ?? '');

    ?>

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <!-- Main content -->
        <section class="content">

            <div class="container-fluid pt-4">

                <div class="widget-user-header bg-default p-2 px-0">
                <span class="float-right" style="float: right">
                    <a target="_blank" href="/">
                        <i class="far fa-hand-grab-o"></i>
                        <a style="" target="_blank"
                           href="https://docs.google.com/document/d/1ys5XYvriQVbgczb84cYW0f0J4kAk6EixEP-LE0GzbF0/edit">
                            <i class="fa fa-book"></i>
                        Hướng dẫn sử dụng
                        </a>
                    </a>
                </span>
                    <i class="fas fa-calendar-alt"></i>

                    TỔNG HỢP SỰ KIỆN

                    <?php
                    //Lấy Tên phòng của user
                    if ($isMember ?? '') {
                        $dep_idx = \App\Models\EventInfo::getDepartmentIdOfUser(getUserIdCurrent_());
                        if ($depx = \App\Models\Department::find($dep_idx))
                            echo "<span class='text-uppercase'> - <b> " . $depx->name . " </b> </span>";
                    }
                    ?>

                    </h5>

                </div>
                <div class="row pt-1">


                    <?php
                    $tt = 0;
                    if ($isMember ?? '')
                        $tt = count(\App\Models\EventInfo::getEventIdListInDeparmentOfUser(getUserIdCurrent_()));
                    else {
                        $tt = \App\Models\EventInfo::count();

                        if ($dep_id ?? '')
                            $tt = \App\Models\EventInfo::where('department', $dep_id)->count();
                    }
                    ?>
                    <?php
                    if (!($isMember ?? '')){

                        ?>
                    <div class="col-md-4 col-sm-6 col-12">
                        <div class="info-box bg-info" style="">
                            <span class="info-box-icon"><i class="fa faw fa-home"></i></span>
                            <div class="info-box-content">

                                    <?php
                                    $link0 = \LadLib\Common\UrlHelper1::getUriWithoutParam();
                                    ?>

                                <select class="form-control"
                                        style="background: snow;  <?php if($dep_id) echo "font-weight: bold; color: royalblue" ?> "
                                        onchange="window.location.href= '{{$link0}}?dep_id=' + this.value"
                                >
                                    <option value="0"> - Chọn Đơn vị, Phòng ban -</option>
                                        <?php

                                        $mDep = \App\Models\Department::all();
                                        foreach ($mDep as $dep) {
                                            if ($dep->id == $dep_id)
                                                echo "<option value='$dep->id' selected>$dep->name</option>";
                                            else
                                                echo "<option value='$dep->id'>$dep->name</option>";
                                        }
                                        ?>


                                </select>


                                {{--                                <span class="info-box-number">41,410</span>--}}
                                {{--                                <div class="progress">--}}
                                {{--                                    <div class="progress-bar" style="width: 70%"></div>--}}
                                {{--                                </div>--}}
                                {{--                                <span class="progress-description">--}}
                                {{--70% Increase in 30 Days--}}
                                {{--</span>--}}
                            </div>

                        </div>

                    </div>

                        <?php
                    }
                    ?>
                    <div class="col-md-4 col-sm-6 col-12">
                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="far fa-bookmark"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text"> Số sự kiện </span>
                                <span class="info-box-number">
                                    {{
    ($tt)


}}
                                                                    </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 col-12">
                        <div class="info-box bg-warning">
                            <span class="info-box-icon"><i class="far fa-calendar-alt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Số tin nhắn đã gửi</span>
                                <span class="info-box-number">...</span>

                            </div>

                        </div>

                    </div>


                </div>

            </div>

            <div class="container-fluid">


                <div class="widget-user-header bg-default p-2 px-0">

            <span class="float-right" style="float: right">
                <a href="/{{$admin}}/event-info">
                    <i class="fa fa-external-link-alt"></i>
                Xem tất cả
                </a>
            </span>

                    <i class="fas fa-user-friends MR-1"></i>
                    SỰ KIỆN GẦN ĐÂY
                    <?php
                    if ($dep_id) {
                        $dep = \App\Models\Department::find($dep_id);
                        echo "<span class='text-uppercase'> - <b>" . $dep->name . " </b></span>";
                    }
                    ?>

                </div>
                <div class="row">

                    <?php


                    if ($isMember ?? '') {
                        //Lấy sự kiện gần đây của department

                        $mmEvId = \App\Models\EventInfo::getEventIdListInDeparmentOfUser(getUserIdCurrent_());
                        $mev = \App\Models\EventInfo::
                        whereIn('id', $mmEvId)
                            ->orderBy('time_start', 'desc')
                            ->take(12)
                            ->get();
                    } else {

                        $mev = \App\Models\EventInfo::
                        orderBy('time_start', 'desc')
                            ->take(12);

                        if ($dep_id) {
                            $mev = $mev->where('department', $dep_id);
                        }
                        $mev = $mev->get();
                    }

// Usage
                    $mev = sortAndMergeEvents($mev);


                    foreach ($mev as $ev){
                        $mEu = \App\Models\EventAndUser::where('event_id', $ev->id)->get();
                        $nInvite = count($mEu);
                        $nAttend = $nDenyJoin = $nConfirmJoin = 0;
                        foreach ($mEu AS $eu) {
                            if ($eu->confirm_join_at) {
                                $nConfirmJoin++;
                            }
                            if ($eu->deny_join_at) {
                                $nDenyJoin++;
                            }
                            if ($eu->attend_at) {
                                $nAttend++;
                            }

                        }
                        $nNotReply = $nInvite - $nConfirmJoin - $nDenyJoin;
                        ?>
                    <div class="col-md-4">
                        <div class="card card-widget widget-user-2 shadow-sm mt-2" data-code-pos='ppp17320755683961'>
                            <div class="widget-user-header bg-info p-3 px-3" data-code-pos='ppp17320755661801'>
                            <span class="">
                                <div class="text_cut" title="{{$ev->name}}">
                                    <a style="; color: white" href="/{{$admin}}/event-info/edit/{{ $ev->id }}">
                                    <i class="far fa-clock"></i>
                                    {{$ev->name}}

                                        </a>

                                </div>
                            </span>

                            </div>

                            <div class="card-footer p-0">
                                <ul class="flex-column" style="list-style: none; padding-left: 1px">
                                    <li class="nav-item">
                                        <div class="nav-link">
                                            Thời gian
                                            <span class="">
                                            <?php

                                                    $timeEnd = $ev->time_end;
                                                    if (substr($ev->time_start, 0, 10) == substr($ev->time_end, 0, 10))
                                                        $timeEnd = substr(substr($ev->time_end, 11), 0, 5);
                                                    else
                                                        $timeEnd = $ev->time_end ? substr(\LadLib\Common\clsDateTime2::convertToTimeVnFormat(strtotime($ev->time_end)), 0, 16) : '?';


                                                if ($ev->time_start){
                                                    ?>
                                                {{
                                                    $ev->time_start ? substr(\LadLib\Common\clsDateTime2::convertToTimeVnFormat(strtotime($ev->time_start)), 0, 16) : '?'
                                                }}
                                            -
                                            {{
                                               $timeEnd
                                            }}
                                                    <?php
                                                }
                                                    ?>
                                        </span>
                                        </div>
                                    </li>
                                    <li class="nav-item">
                                        <div class="nav-link text_cut count_down_p" title="">
                                            <span id="count_down_{{$ev->id}}" class="count_down"
                                                  style="">
                                                <script>
                                                    const timer{{$ev->id}} = new CountdownTimer('{{$ev->time_start}}', '{{$ev->time_end}}', 'count_down_{{$ev->id}}');
                                                    timer{{$ev->id}}.start();
                                                </script>
                                            </span>
                                        </div>
                                    </li>
                                    <li class="nav-item">
                                        <div class="nav-link text_cut" title="{{$ev->location}}">
                                            Địa điểm:
                                            <span style="">

                                            {{ \LadLib\Common\cstring2::substr_fit_char_unicode($ev->location, 0, 50,1) }}

</span>
                                        </div>
                                    </li>
                                    <li class="nav-item">
                                        <div class="nav-link">
                                            Tổng số khách mời <span class="float-right badge bg-info">
                                                <?php

                                                    $key = $metaEventAndUser->getShortNameFromField('event_id');

                                                    $link = "/$admin/event-and-user?seby_$key=$ev->id";

                                                    ?>

                                            <a href="{{$link}}">
                                            {{
                                            count($mEu);
                                            }}
                                            </a>

                                        </span>
                                        </div>
                                    </li>
                                    <li class="nav-item">
                                    <span class="nav-link">
                                        Khách xác nhận tham dự <span class="float-right badge bg-info">
                                            <a href="{{$link}}">
                                            {{$nConfirmJoin}}
                                            </a>
                                        </span>
                                    </span>
                                    </li>
                                    <li class="nav-item">
                                        <div class="nav-link">
                                            Khách checked-in <span class="float-right badge bg-primary">
                                            <a href="{{$link}}">
                                            {{$nAttend}}
                                            </a>
                                        </span>
                                        </div>
                                    </li>

                                    <li class="nav-item">
                                        <div class="nav-link">
                                            Khách từ chối tham dự
                                            <span class="float-right badge bg-warning">
                                            <a href="{{$link}}">
                                            {{$nDenyJoin}}
                                            </a>
                                        </span>
                                        </div>
                                    </li>
                                    <li class="nav-item">
                                        <div class="nav-link">
                                            Người tạo:
                                            <span style="">
                                            {{$ev->getAdminEmail()}}

                                        </span>
                                        </div>
                                    </li>
                                    <li class="nav-item">
                                        <div class="nav-link">
                                            Đơn vị tổ chức:
                                            <span style="">
                                            {{$ev->getDepartmentName()}}
</span>

                                        </div>
                                    </li>
                                    {{--                                <li class="nav-item">--}}
                                    {{--                                    <span class="nav-link">--}}
                                    {{--                                        Số Email đã gửi <span class="float-right badge bg-light">--}}
                                    {{--                                            842--}}
                                    {{--                                        </span>--}}
                                    {{--                                    </span>--}}
                                    {{--                                </li>--}}
                                    {{--                                <li class="nav-item">--}}
                                    {{--                                    <span class="nav-link">--}}
                                    {{--                                        Số SMS đã gửi <span class="float-right badge bg-light">--}}
                                    {{--                                            123--}}
                                    {{--                                        </span>--}}
                                    {{--                                    </span>--}}
                                    {{--                                </li>--}}
                                </ul>
                            </div>
                        </div>
                    </div>
                        <?php
                    }
                    ?>

                </div>
            </div>

        </section>
        <!-- /.content -->
    </div>
@endsection
