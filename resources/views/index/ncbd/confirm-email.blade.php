@extends(getLayoutNameMultiReturnDefaultIfNull())



@section('title')
    <?php
//    echo \App\Models\SiteMng::getTitle();
    ?>
    EVENT MANAGEMENT SYSTEM - Viện NCBD

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

    <div class="container" style="min-height: 600px">
        <?php

        if($dt = request('data')){
            list($idEv, $data) = explode('|', $dt);
//            echo "<br/>\n ID = $id  / $data";
        }else{
            $idEv = \request('id');
            $data = \request('data_ev');

        }
        $idEv = qqgetIdFromRand_($idEv);
        $lang = request('lang', 'vi'); // Default to Vietnamese

        if (!is_numeric($idEv)) {
            if ($lang == 'en') {
                bl("Not valid event ID!");
            } else {
                bl("ID sự kiện không hợp lệ!");
            }
            return;
        }

//        $emailOrUid = $data;
//        if(!is_numeric($data))
            $emailOrUid = dfh1b($data);

        if (!filter_var($emailOrUid, FILTER_VALIDATE_EMAIL) && !is_numeric($emailOrUid)) {
            if ($lang == 'en') {
                bl("Invalid information: $emailOrUid");
            } else {
                bl("Thông tin không hợp lệ: $emailOrUid");
            }
            return;
        }

        if (!$ev = \App\Models\EventInfo::find($idEv)) {
            if ($lang == 'en') {
                bl("Event not found!");
            } else {
                bl("Không tìm thấy sự kiện!");
            }
            return;
        }

        if(is_numeric($emailOrUid))
            $eu = \App\Models\EventUserInfo::find($emailOrUid);
        else
            $eu = \App\Models\EventUserInfo::where("email", $emailOrUid)->first();
        if (!$eu) {
            if ($lang == 'en') {
                bl("User not found: $emailOrUid");
            } else {
                bl("Không tìm thấy người dùng: $emailOrUid");
            }
            return;
        }

        if (!$eau = \App\Models\EventAndUser::where(["user_event_id" => $eu->id, 'event_id' => $idEv])->first()) {
            if ($lang == 'en') {
                bl("User registration for this event not found!");
            } else {
                bl("Không tìm thấy đăng ký người dùng cho sự kiện này!");
            }
            return;
        }
        ?>

        <br>
        <div class='p-4 rounded text-left mt-3' style="background-color: lavender">
            <?php

                $lang = $eu->language ?? 'vi';

                // Default messages for Vietnamese
                if ($lang == 'vi') {
                    $txt = "\n Xin chào <b>  $eu->title $eu->last_name $eu->first_name </b>".
                         "<br/>\nMời quý vị Xác nhận tham dự Sự kiện:  ".
                         "<br/>\n <b> $ev->name </b>".
                         "<p>\n <i style='font-size: small'> Thời gian: $ev->time_start | $ev->time_end</i> </p>".
                         "<p>\n <i> Xin cảm ơn Quý vị! </i>  </p>";

                    if ($ev->web_text_confirm_join_event_vi) {
                        $txt = $ev->web_text_confirm_join_event_vi;
                    }
                    $confirmBtnText = 'Xác nhận Tham dự';
                    $notConfirmBtnText = 'Từ chối Tham dự';
                }
                // English version
                else {
                    $txt = "\nGreetings <b> $eu->title $eu->last_name $eu->first_name </b>".
                         "<br/>\nPlease confirm participation to the event:  ".
                         "<br/>\n <b> $ev->name </b>".
                         "<p>\n <i style='font-size: small'> Time: $ev->time_start | $ev->time_end</i> </p>".
                         "<p>\n <i> Thank you! </i>  </p>";

                    if ($ev->web_text_confirm_join_event_en) {
                        $txt = $ev->web_text_confirm_join_event_en;
                    }
                    $confirmBtnText = 'Yes, I will participate';
                    $notConfirmBtnText = 'No, I cannot participate';
                }

                $txt = \App\Models\EventInfo::replaceAllMarkText($txt, $ev, $eu);
                $txt = str_replace("\n", "<br/>\n", $txt);

                echo $txt;


            ?>

            <form class="mt-3" method="post" action="<?php echo \LadLib\Common\UrlHelper1::getUrlRequestUri() ?>"
                  style="max-width: 500px;">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input class=" btn btn-success btn-sm mb-1 d-inline-block" type="submit" name="confirm"
                       value="{{$confirmBtnText}}">
                <input class=" btn btn-danger btn-sm mb-1 d-inline-block mx-3" type="submit" name="reject"
                       value="{{$notConfirmBtnText}}">
            </form>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $lang = $eu->language ?? 'vi';

                if (isset($_POST['confirm'])) {
                    // Handle confirmation button
                    $eau->confirm_join_at = nowyh();
                    $eau->deny_join_at = null;
                    $eau->addLog("Confirm join via email");
                    $eau->save();
                    echo "<br/>\n";

                    if ($lang == 'vi') {
                        tb("Quý khách đã xác nhận tham gia!");
                    } else {
                        tb("You have confirmed to join the event!");
                    }
                } elseif (isset($_POST['reject'])) {
                    // Handle rejection button
                    $eau->deny_join_at = nowyh();
                    $eau->confirm_join_at = null;
                    $eau->addLog("Rejected join via email");
                    $eau->save();
                    echo "<br/>\n";

                    if ($lang == 'vi') {
                        bl("Quý khách đã từ chối tham gia!");
                    } else {
                        bl("You have rejected to join the event!");
                    }
                }
            }
            ?>
        </div>
    </div>
@endsection
