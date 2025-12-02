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

        if (!is_numeric($idEv)) {
            bl("Not valid id event!");
            return;
        }

//        $emailOrUid = $data;
//        if(!is_numeric($data))
            $emailOrUid = dfh1b($data);

        if (!filter_var($emailOrUid, FILTER_VALIDATE_EMAIL) && !is_numeric($emailOrUid)) {
            bl("Not valid info: $emailOrUid");
            return;
        }

        if (!$ev = \App\Models\EventInfo::find($idEv)) {
            bl("Not found event!");
            return;
        }

        if(is_numeric($emailOrUid))
            $eu = \App\Models\EventUserInfo::find($emailOrUid);
        else
            $eu = \App\Models\EventUserInfo::where("email", $emailOrUid)->first();
        if (!$eu) {
            bl("Not found user: $emailOrUid");
            return;
        }

        if (!$eau = \App\Models\EventAndUser::where(["user_event_id" => $eu->id, 'event_id' => $idEv])->first()) {
            bl("Not found user with event!");
            return;
        }
        ?>

        <br>
        <div class='p-2 rounded text-left mt-3' style="background-color: lavender">
            <?php

                $txt =  "\n Xin chào <b>  $eu->title $eu->last_name $eu->first_name </b>".
             "<br/>\nMời quý vị bấm vào Link dưới đây để Xác nhận tham dự Sự kiện:  ".
             "<br/>\n <b> $ev->name </b>".
             "<p>\n <i style='font-size: small'> Thời gian: $ev->time_start | $ev->time_end</i> </p>".
             "<p>\n <i> Xin cảm ơn Quý vị! </i>  </p>";

                $confirmBtnText = 'Xác nhận Tham dự';
                $notConfirmBtnText = 'Từ chối Tham dự';
                if($eu->language == 'en'){
                    $confirmBtnText = 'Yes, I will participate';
                    $notConfirmBtnText = 'No, I cannot participate';
                    if($ev->web_text_confirm_join_event_en){
                        $txt = $ev->web_text_confirm_join_event_en;
                    }
                }
                if($eu->language == 'vi'){
                    if($ev->web_text_confirm_join_event_vi){
                        $txt = $ev->web_text_confirm_join_event_vi;
                    }
                }

            $txt = \App\Models\EventInfo::replaceAllMarkText($txt,  $ev, $eu);

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
                if (isset($_POST['confirm'])) {
                    // Xử lý khi nút "Xác nhận" được nhấn
                    $eau->confirm_join_at = nowyh();
                    $eau->deny_join_at = null;
                    $eau->addLog("Confirm join email");
                    $eau->save();
                    echo "<br/>\n";
                    tb("Quý khách đã xác nhận tham gia!", "You have confirmed to join the event!");
                } elseif (isset($_POST['reject'])) {
                    // Xử lý khi nút "Từ chối" được nhấn
                    $eau->deny_join_at = nowyh();
                    $eau->confirm_join_at = null;
                    $eau->addLog("Confirm join email");
                    $eau->save();
                    echo "<br/>\n";
                    bl("Quý khách đã Từ chối tham gia!", "You have rejected to join the event!");
                } else {
                }
            }
            ?>
        </div>
    </div>
@endsection
