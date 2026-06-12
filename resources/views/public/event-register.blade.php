<?php

if ($ide = request('id')) {

    if (is_numeric($ide)) {
        bl("Not valid event id");
        return;
    }
    $idEv0 = $id = qqgetIdFromRand_($ide);
    $ev = \App\Models\EventInfo::find($id);
    if($ev instanceof \App\Models\EventInfo) ;
    if (!$ev) {
        bl('Event not found');
        return;
    }


}



?>
@extends(getLayoutNameMultiReturnDefaultIfNull())

<?php

$mt = new \App\Models\EventRegister_Meta();
$mt->extraCssIncludeEdit();

?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/choices.js/10.2.0/choices.min.css" />

<style>
    .selected-info {
        margin-top: 20px;
        padding: 15px;
        background-color: #e9f4ff;
        border-radius: 5px;
        border-left: 4px solid #007bff;
        display: none;
    }
    .choices__inner {
        min-height: auto!important;
        font-size: inherit!important;
        border: 1px solid #ddd!important;
    }
    .bank-info {
        margin: 5px 0;
    }
    .bank-info strong {
        color: #007bff;
    }
    /* Customize Choices.js */
    .choices {
        margin-bottom: 15px;
    }
    .choices__list--single {
        padding: 0px
    }
    .choices[data-type*=select-one] .choices__inner {
        /*padding: 0px*/

    }
    .choices__inner {
        background-color: #ffffff;
        border: 2px solid #ddd;
        border-radius: 5px;
        font-size: 16px;
        min-height: 44px;
        padding: 5.5px 7.5px 2.75px;
    }
    .choices__inner:focus {
        border-color: #007bff;
    }
    .choices__list--dropdown {
        border: 1px solid #007bff;
        border-radius: 5px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .choices__item--choice:hover {
        background-color: #f8f9fa;
    }
    .choices__item--choice.is-highlighted {
        background-color: #007bff;
    }
    .search-hint {
        font-size: 14px;
        color: #666;
        margin-top: 5px;
        font-style: italic;
    }
</style>
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

    .form-group {
        margin-top: 1rem;
        margin-bottom: 1rem;
    }

    .img_ev {
        width: 100%;
    }
    .sub_event_zone label {
        display: inline!important;
    }

    .sub_event_zone .iconx{
        /*min-width: 20px;*/
    }

    .sub_event_info {
        padding: 0px!important;
    }


    .sub_event_info td {
        padding: 15px;
        line-height: 140%;
        color: #686868; font-size: 92%
    }

    .first_td {
        /*background-color: #0a6aa1;*/
        border-right: 1px dashed #eee;
    }


        .check_sub_event{

    }

    .footer {
        <?php
if($id ?? ''){
?>
           position: relative !important;
    <?php
}
?>



    }

    .top-nav-zone {
        position: relative !important;
    }

    .main_form {
        /*box-shadow: 0px 0px 10px 10px #eee;*/
        box-shadow: 0px 0px 10px 5px #6f6d6dab !important;
        /*background: url('/images/graphics/background_1.jpg') no-repeat center center;*/
        /*background-size: cover;*/

    }
</style>


@section('title', 'Event Registration')


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

<?php
\App\Models\BlockUi::showCssHoverBlock();
?>
@section('content')
    <div class="container mt-5" style="">
        <div class="row justify-content-center">
            <div class="col-md-8" data-code-pos='ppp17297660616151' style="min-height: 700px">
                <?php
//                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                print_r(request()->all());
//                echo "</pre>";
                $lang0 = $lang = App::getLocale();



                if ($regCode = request('reg_code')) {

                    $regCode = trim($regCode);
                    $regCode = explode('?', $regCode)[0];



//                    if(isAdminCookie())
//                       bl("Mã đăng ký: $regCode");

                    //Tìm trong db:
                    $evReg = \App\Models\EventRegister::where('reg_code', $regCode)->first();
                    if ($evReg) {

                        $id = $evReg->event_id;


                        $ev = \App\Models\EventInfo::find($id);
                        $evName = $ev->name;

                        if($evReg->lang == 'vi'){
                            $txt1 = "Xin chào $evReg->title <b> $evReg->first_name $evReg->last_name </b> ($evReg->email) <br> Bạn đã đăng ký vào sự kiện:<br> <b>$evName</b><br> Bạn vui lòng chờ duyệt từ Admin. <br> Cảm ơn bạn!";

                            $txt1 = "$ev->reg_text_vn";

//                            \App\Models\EventInfo::replaceAllMarkText();

                        }
                        else{
                            $txt1 = "Greetings $evReg->title <b> $evReg->first_name $evReg->last_name </b> ($evReg->email) <br> We received your registration for the event:<br> <b>$evName</b><br> Please wait for Approval. <br> Thank you!";
                            $txt1 = "$ev->reg_text_en";
                        }
//                        if(isAdminCookie())
//                            bl("Mã idx: $id / $txt1");
                        $email = $evReg->email;

//                        $regCode = eth1b($evReg->id . "." . $email . "." . microtime());
//                        $linkRegister = "https://" . \LadLib\Common\UrlHelper1::getDomainHostName() . "/event-register/verify-email/$regCode";

                        $mmReplace = [
                            \App\Models\EventInfo::$DEF_TENKHACH[0] => "$evReg->first_name $evReg->last_name",
                            \App\Models\EventInfo::$DEF_USER_NAME[0]=> "$evReg->first_name $evReg->last_name",
                            \App\Models\EventInfo::$DEF_EVENT_NAME[0]=> $ev->name,
//                            \App\Models\EventInfo::$DEF_REG_LINK_OLD[0] => $linkRegister,
//                            \App\Models\EventInfo::$DEF_CONFIRM_EMAIL[0] => $linkRegister,
                            "\n" => "<br>",
                        ];

//                        $txt1 = cstring2::replaceByArray($txt1, $mmReplace);

//                        $txt1 = "Xin chào <b> $evReg->first_name $evReg->last_name </b> ($evReg->email) <br> Bạn đã đăng ký vào sự kiện:<br> <b>$evName</b><br> Bạn vui long chờ duyệt từ Admin. <br> Cảm ơn bạn!";

//                        if(isAdminCookie()) {
//                            bl("DEBUG1  Mã đăng ký1: $regCode");
//                            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                            print_r($evReg->toArray());
//                            echo "</pre>";
//                            }



                        if ($evReg->reg_confirm_time) {

//                            bl("DEBUG1");

                            if ($uEOld = \App\Models\EventUserInfo::where('email', $evReg->email)->first()) {
                                if(isAdminCookie()) {
//                                    bl("DEBUGe Thay user:");
                                }

                                $uidEvOld = $uEOld->id;
                                $checkInEV = \App\Models\EventAndUser::where('event_id', $id)->where('user_event_id', $uidEvOld)->first();
                                if ($checkInEV) {
//                                    bl("DEBUG2");
                                    if($evReg->lang == 'vi') {

                                        $txt1 = "$ev->reg_text_ok_vn";
                                        //tb("Xin chào $evReg->first_name $evReg->last_name ($evReg->email)  <br> Bạn đã đăng ký vào sự kiện:<br> <b>$evName</b> <br> và đã được duyệt vào sự kiện này!");
                                    }
                                    else{

                                        $txt1 = "$ev->reg_text_ok_en";
                                        //tb("Greetings $evReg->first_name $evReg->last_name ($evReg->email)  <br> We received your registration for the event:<br> <b>$evName</b> <br> and you have been approved for this event!");
                                    }

                                }
//                                else
//                                    tb($txt1);
                            }
//                            else
//                                tb($txt1);

//                        goto _END;
                        } else {
                            $evReg->reg_confirm_time = nowyh();
                            $evReg->addLog("Confirm reg by email: $evReg->email, IP:" . @$_SERVER['REMOTE_ADDR'] . ", Refer: " . @$_SERVER['HTTP_REFERER']);
                            $evReg->save();

                        }

                        $txt1 = \LadLib\Common\cstring2::replaceByArray($txt1, $mmReplace);
                        if($txt1)
                            tb($txt1);
                        else {
                            bl("Registration successful. Please wait for Admin approval.");
                        }

                    } else {
                        bl("Mã đăng ký không hợp lệ!");
                    }
                    goto _END;
                }

                if (!$ev) {
                    echo 'Event not found';
                    goto _END;
                }

                if ($ev instanceof \App\Models\EventInfo) ;
                $img = $ev->getThumbInImageList('image_register');
                $title= trim(request('title'));
                //sinh ra  các biến lấy từ form
                $first_name = trim(request('first_name'));
                $last_name = trim(request('last_name'));

                $gender = trim(request('gender'));
                $designation = trim(request('designation'));

                $id_number = trim(request('id_number'));
                $tax_number = trim(request('tax_number'));
                $email = trim(request('email'));
                $phone = trim(request('phone'));

                $bank_acc_number = trim(request('bank_acc_number'));
                $bank_name_text = trim(request('bank_name_text'));

                //Thay thế tất cả ký tự không phải số bằng rỗng
                $phone = preg_replace('/\D/', '', $phone);

                $phone = \App\Models\EventUserInfo::fixPhoneNumber($phone);

                $address = trim(request('address'));
                $organization = trim(request('organization'));
                $note = trim(request('note'));

                $regDone = 0;

                if ($_POST){
                if ($first_name && $last_name && $email) {

                    //Kiểm tra $bank_name_text phải nằm trong danh sách ngân hàng được phép
                    $allowedBanks = config('banks');

                    $bank_name_text = trim($bank_name_text);
                    if($bank_name_text)
                    if (!array_key_exists($bank_name_text, $allowedBanks)) {
                        bl("Ngân hàng không tồn tại? Not found bank?" . $bank_name_text);
                        goto _END;
//                    $bank_name_text = '';
                    }

                    //kiểm tra email đã đăng ký chưa
                    $checkInER = \App\Models\EventRegister::where('email', $email)->where('event_id', $id)->first();

                    //Kiem tra trong ca ban user old, va event Info
                    $checkInEV = $uidEvOld = 0;
                    if ($uEOld = \App\Models\EventUserInfo::where('email', $email)->first()) {
                        $uidEvOld = $uEOld->id;
                        $checkInEV = \App\Models\EventAndUser::where('event_id', $id)->where('user_event_id', $uidEvOld)->first();
                    }

                if ($checkInEV) {




                    ?>
                <div class='alert alert-danger' role='alert'>
                    {{ __('Email (:email) already registered for this event (:id) and approved by Admin', ['email' => $email, 'id' => $id]) }}
                    @if ($checkInER)
                        <br>{{ __('Registration time: :created_at', ['created_at' => $checkInER->created_at]) }}
                    @endif
                    <br>{{ __('Approved time: :created_at', ['created_at' => $checkInEV->created_at]) }}
                </div>
                    <?php
                } //Nếu đã đăng ký trong 1 trong 2 bảng
                elseif ($checkInER) {



                    $pad = '';
                    if (!$checkInER->reg_confirm_time) {
                        $pad = ", Please check your email ";
                    } else {
                        $pad = " at $checkInER->reg_confirm_time";
                    }

                    ?>

                <div class='alert alert-danger' role='alert'>

                    {{ __('Email already registered :pad for this event (:id) and waiting for Admin approval', ['pad' => $pad, 'id' => $id]) }}
                     <br> - (RegID: {{ $checkInER->id }})
                </div>
                    <?php

                } else {

                    if (!$lang)
                        $lang = 'vi';
                    if ($lang != 'en' && $lang != 'vi')
                        $lang = 'vi'; //Mặc định là 'en

                //Sub Event List



                    $subEventList = '';
                    $mmSubEvent = [];
                    //Nếu có subevent id sub_event_
                    foreach ($_POST AS $k => $v) {
                        if (strpos($k, 'sub_event_') === 0) {
                            $subEventId = str_replace('sub_event_', '', $k);
                            //Đưa user vào sub event này:
                            if (is_numeric($subEventId) && \App\Models\EventInfo::find($subEventId))
                                $subEventList .= $subEventId . ',';
                            $mmSubEvent[] = $subEventId;
                        }
                    }
                    $subEventList = trim($subEventList, ',');



                    //Thêm đoạn upload photo_file ở đây, là dạng file thì code ra sao?
                    //https://www.tutorialrepublic.com/php-tutorial/php-file-upload.php
                    //Viết code cụ thể cho tôi:
                    // Inside your form processing logic
                    $data = [
                        'title' => $title,
                        'first_name' => $first_name,
                        'gender' => $gender,
                        'designation' => $designation,
                        'id_number' => $id_number,
                        'tax_number' => $tax_number,
                        'lang' => $lang,
                        'last_name' => $last_name,
                        'email' => $email,
                        'phone' => $phone,
                        'address' => $address,
                        'organization' => $organization,
                        'note' => $note,
                        'event_id' => $id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'sub_event_list' => $subEventList,
                        'bank_acc_number' => $bank_acc_number,
                        'bank_name_text' => $bank_name_text,
                    ];


//                    if (!request()->hasFile('photo_file')){
//                        echo '<div class="alert alert-danger" role="alert">
//                        Please upload your photo
//                    </div>';
//                        goto _BEGIN_FORM;
//                    }
//                    else

                    {

//                        echo "<br/>\n photo 1";
//                        $validator = Validator::make(request()->all(), [
////                            'photo_file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
//                            'photo_file' => 'image|mimes:jpeg,png,jpg,gif|max',
//                        ]);
//                        if ($validator->fails()) {
//                            return back()->withErrors($validator)->withInput();
//                        }


                        if ($resizedImageData = request('resized_image_data')) {

                            $mtime = microtime(1);

                            $resizedImageData = request('resized_image_data');
                            $file = dataURLtoFile($resizedImageData, "/share/resized_image.$mtime.jpg");
                            // Lưu file vào hệ thống
                            $filePath = '/share/' . $file->getClientOriginalName();
                            file_put_contents($filePath, file_get_contents($file->getPathname()));
                            $std = new stdClass();
                            $std->file_name = 'reg.avatar.' . $mtime . '.jpg';
                            $std->file_size = filesize($filePath);
                            $std->file_path_local_upload_ = $filePath;
                            $std->user_id = getUserIdByEmail_('dungla2011@gmail.com');

                            $ret = \App\Http\ControllerApi\FileUploadControllerApi::uploadStatic($std, 1);
                            if (file_exists($filePath))
                                unlink($filePath);

                            if($ret instanceof \Illuminate\Http\JsonResponse) {
                                $data = $ret->getData();
                                if ($data->code ?? '') {
                                    if ($data->code == -1) {
                                        bl("Error: Can not upload image ($data->message)");
                                        return;
                                    }
                                }
                            }

                            $data['image_list'] = $ret[0]?->id;
                            if(!$data['image_list']){
                                bl("Error: Can not upload image!");
                                return;
                            }

                        }else{

//                            if($ev->user_need_image_to_reg){
//
////                                bl("Please upload your photo");
////                                goto _END;
//
//                            }


                        }


                        if (0)
                            if (request()->hasFile('photo_file')) {
                                $file = request()->file('photo_file');
                                $filePath = $file->store('/share');

                                $std = new stdClass();
                                $std->file_name = $file->hashName();
                                $std->file_size = filesize($file->path());
                                $std->file_path_local_upload_ = $file->path();
                                $ret = \App\Http\ControllerApi\FileUploadControllerApi::uploadStatic($std, 1);
                                if (file_exists($filePath))
                                    unlink($filePath);
                                $data['image_list'] = $ret[0]->id;
                            }
                    }

                    if ($uid = getCurrentUserId()) {
                        //Không cho uid vào đây, vì có thể là user cũ đăng ký cho user mới
                        //hoặc 1 người đăng ký cho nhiều người khác nhau
                        $data['user_id'] = $uidEvOld;
                    }

                    if ($uidEvOld) {
                        $data['user_event_id'] = $uidEvOld;
                    }
//
//                        if(isDebugIp()){
//                            die("xxxx");
//                        }else
                    $eventRegister = \App\Models\EventRegister::create($data);

                    //Nếu có sub event thì thêm vào bảng event_and_user
                    if ($mmSubEvent) {
                        foreach ($mmSubEvent AS $subEventId) {
                            $data['event_id'] = $subEventId;
                            \App\Models\EventRegister::create($data);
                        }
                    }


                    $newId = $eventRegister->id;

                    /*
reg_mail_title_vi1
reg_mail_01_vi
reg_mail_title_vi2
reg_mail_02_vi
reg_mail_title_en1
reg_mail_01_en
reg_mail_title_en2
reg_mail_02_en
                     */


                    if ($newId) {

                        \App\Http\ControllerApi\EventInfoControllerApi::sendMailRegEvent($newId);

//                        bl("Bạn hãy kiểm tra email để xác nhận đăng ký");
                    }
                    ?>

                <div class="alert alert-warning" role="alert">
                    {{ __('Please check your email (:email) for further information!', ['email' => $email]) }}
                </div>

                    <?php
                }
//                goto _END;
                }


                    $back = \LadLib\Common\UrlHelper1::getUriWithoutParam();
                    echo "<a href='$back'> <button class='btn btn-sm btn-primary'> Go Back </button> </a>";
                }
                else{
                    _BEGIN_FORM:
                    ?>
                <div class="card mb-5 qqqq1111 main_form" data-code-pos='ppp17297660517361'>
                        <?php
                        \App\Models\BlockUi::showEditLink_("/admin/event-info/edit/$id", 'Edit sự kiện này');
                        ?>
                    <div class="card-header bg-darkorange text-center pt-4 px-4" style="
                color: white;
                border-radius: 5px 5px 0px 0px ;
                background-color: #0a6aa1;
                                             text-shadow: 3px 3px 3px #222222;

                ">
                        <div class="mt-1">
                            <h5 style="text-transform: uppercase;
                            color: white;
                             text-shadow: 3px 3px 3px #222222;
                            ">{{ __('reg_event_ncbd.register_event') }}</h5>
                        </div>
                        <b>
                            <h4 class="mb-0 pt-1 pb-3" style="
                    line-height: 30px; text-transform: uppercase;
                    text-shadow: 5px 5px 5px #222222;
                    color: white;
                    ">
                                <b>
                                    {{ $ev->getName($lang0) }}
                                </b>
                            </h4>
                        </b>

                            <?php
                            $strFullTIme = \App\Models\EventInfo::getStrTimeStartEnd($ev);
                            echo "<div class='text-center  mb-2' data-pos='098098090909808'> $strFullTIme </div>";
                            ?>
                            <?php

                            if ($loc1 = $ev->getLocation($lang0))
                                echo "<div class='text-center mb-3'>" . __('reg_event_ncbd.location') . ": " . $loc1 . "</div>";
                            ?>


                    </div>
                    <div class="p-2">
                            <?php

                            if ($img)
                                echo "<img class='img_ev mb-2' src='$img'>";
                            ?>
                    </div>
                    <div class="card-body px-5" data-code-pos='ppp17297660468911'>
                        <form id="formUpload" method="POST" action="" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="resized_image_data" name="resized_image_data">

                            <h4 class="text-center mt-3">
                                {{ __('reg_event_ncbd.enter_info') }}
                            </h4>

                            <div class="row mt-5 mb-0">
                                <div class="form-group col-sm-6 col-md-3">
                                    <label for="title">{{ __('reg_event_ncbd.title') }} * </label>
                                    <select class="form-control" id="title" name="title" required>
                                        <option value="">---</option>
                                        <option value="Ông">Ông</option>
                                        <option value="Bà">Bà</option>
                                        <option value="Anh">Anh</option>
                                        <option value="Chị">Chị</option>
                                        <option value="Mr">Mr</option>
                                        <option value="Mrs">Mrs</option>
                                        <option value="Mrs">Ms</option>
{{--                                        <option value="Mrs">Miss</option>--}}
                                    </select>
                                </div>
                                <div class="form-group col-sm-6 col-md-3 ">
                                    <label for="first_name">{{ __('reg_event_ncbd.first_name') }} * </label>
                                    <input type="text" class="form-control" id="first_name" value="{{$first_name}}"
                                           name="first_name" required>
                                </div>
                                <div class="form-group col-sm-6 col-md-3">
                                    <label for="last_name">{{ __('reg_event_ncbd.last_name') }}  * </label>
                                    <input type="text" class="form-control" id="last_name" value="{{$last_name}}"
                                           name="last_name" required>
                                </div>
                                <div class="form-group col-sm-6 col-md-3">
                                    <label for="gender">{{ __('reg_event_ncbd.gender') }}  *  </label>
                                    <select class="form-control" id="gender" name="gender" required>
                                        <option value="">---</option>
                                        <option value="1">{{ __('reg_event_ncbd.gender_male') }}</option>
                                        <option value="2">{{ __('reg_event_ncbd.gender_female') }}</option>
                                        <option value="2">{{ __('reg_event_ncbd.gender_other') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mt-3 mb-0">
                                <div class="form-group col-md-6">
                                    <label for="email">{{ __('reg_event_ncbd.email') }}  *  </label>
                                    <input type="email" class="form-control" id="email" value="{{$email}}" name="email"
                                           required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="phone">{{ __('reg_event_ncbd.phone') }}  *  </label>
                                    <input type="tel" class="form-control" id="phone" value="{{$phone}}" name="phone">
                                </div>
                            </div>


                            <div class="form-group">
                                <label for="address">{{ __('reg_event_ncbd.address') }}   </label>
                                <input type="text" class="form-control" id="address" value="{{$address}}" name="address"
                                       >
                            </div>
                            <div class="form-group">
                                <label for="organization">{{ __('reg_event_ncbd.organization') }} * </label>
                                <input type="text" class="form-control" id="organization" value="{{$organization}}"
                                       name="organization" required>
                            </div>
                            <div class="form-group">
                                <label for="designation">{{ __('reg_event_ncbd.designation') }}</label>
                                <input type="input" class="form-control" id="designation" value="{{$designation}}"
                                       name="designation">
                            </div>
                            <div class="form-group">
                                <label for="id_number">{{ __('reg_event_ncbd.id_number') }}</label>
                                <input type="input" class="form-control" id="id_number" value="{{$id_number ?? ''}}"
                                       name="id_number" pattern="[A-Za-z0-9]+" maxlength="32"
                                       title="{{ __('reg_event_ncbd.alphanumeric_only') }}">
                            </div>

                            <?php
                            if($ev->enable_reg_bank){
                            ?>

                            <div class="form-group">
                                <label for="tax_number">{{ __('reg_event_ncbd.tax_number') }}</label>
                                <input required type="input" class="form-control" id="tax_number" value="{{$tax_number ?? ''}}"
                                       name="tax_number" pattern="[A-Za-z0-9]+" maxlength="32"
                                       title="{{ __('reg_event_ncbd.alphanumeric_only') }}">
                            </div>

                            <div class="row mt-0 mb-0">


                            <div class="form-group col-md-8">

                                    <label for="bankSelect">{{ __('reg_event_ncbd.bank_text_select') }}</label>
                                    <select required id="bankSelect">
                                        <option value="">-- {{ __('reg_event_ncbd.bank_text_select') }} --</option>

                                        <!--  Sort banks by code   -->


                                        @php
                                            $banks = config('banks');
                                            asort($banks);
                                            foreach($banks as $code => $name){
                                                 $name = $name['public_name'] ?? '';
                                                echo "<option value='$code'>" . $name . "</option>";
                                            }
                                        @endphp

                                    </select>

{{--                                <div id="bankInfo" class="selected-info">--}}
{{--                                    <h3>📋 Thông tin ngân hàng đã chọn:</h3>--}}
{{--                                    <div class="bank-info"><strong>🏷️ Tên thương hiệu:</strong> <span id="brandName"></span></div>--}}
{{--                                    <div class="bank-info"><strong>🏢 Tên đầy đủ:</strong> <span id="fullName"></span></div>--}}
{{--                                    <div class="bank-info"><strong>🌐 Tên tiếng Anh:</strong> <span id="englishName"></span></div>--}}
{{--                                    <div class="bank-info"><strong>📊 Phân loại:</strong> <span id="category"></span></div>--}}
{{--                                </div>--}}


                                <input required type="hidden" id="bank_name_text" name="bank_name_text" value="">

                            </div>


                            <div class="form-group col-md-4">
                                <label for="bank_acc_number">{{ __('reg_event_ncbd.bank_acc_number') }}</label>
                                <input required type="number" class="form-control" id="bank_acc_number" value="{{$bank_acc_number}}"
                                       name="bank_acc_number" >
                            </div>
                            </div>
                                <?php
                            }
                                ?>

                            <div class="form-group">
                                <label for="photo_file">{{ __('reg_event_ncbd.photo') }} <?php

                                        if($ev->user_need_image_to_reg)
                                            echo "*";

                                ?>  </label>
                                <input type="file" class="form-control" id="photo_file" value="" name="photo_file">
                                <br>
                                <img id="preview_img" src="#" alt="Ảnh xem trước"
                                     style="display: none; max-width: 50%; height: auto;">

                            </div>
                            <div class="form-group">
                                <label for="note">{{ __('reg_event_ncbd.note') }}</label>
                                <textarea class="form-control" id="note" name="note" rows="3">{{$note}}</textarea>
                            </div>

                                <?php


                            if (App\Models\EventInfo::where('parent_id', $ev->id)->first()){
                                ?>

                            <div class="form-group container_sub_event_list" data-code-pos='ppp17357844337151'
                                 style="margin-top: 30px;color: #585858; text-align: center">
                                <b style="text-transform: uppercase">
                                <label style="font-size: 110%"  for="note">{{ __('reg_event_ncbd.sub_event_title') }} </label>
                                </b>
                                <br>
                                {!!  \App\Models\EventInfo::htmlSubEventInputCheck($ev) !!}

                            </div>

                                <?php
                            }
                                ?>

                            <div class="text-center">
                                <button type="submit"
                                        class="my-3 btn btn-primary">{{ __('reg_event_ncbd.register') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                    <?php
                }
                _END:

                ?>
            </div>
        </div>
    </div>


    <?php
    $mt->extraJsIncludeEdit();
    ?>

    <script>


        <?php
            if($ev->user_need_image_to_reg ?? ''){
            ?>

        document.addEventListener('DOMContentLoaded', function() {
            // Get the form and photo input elements
            const registrationForm = document.getElementById('formUpload');
            const photoInput = document.getElementById('photo_file');

            // Check if elements exist before adding listeners
            if (!registrationForm || !photoInput) return;

            // Add submit event listener to the form
            registrationForm.addEventListener('submit', function(event) {
                // Check if the photo input is empty
                if (!photoInput.files || photoInput.files.length === 0) {
                    // Prevent form submission
                    event.preventDefault();

                    // Alert the user
                    alert('Vui lòng tải lên ảnh của bạn trước khi đăng ký.');

                    // Focus on the photo input
                    photoInput.focus();
                }
            });
        });
        <?php
        } ?>

        // Wrap photo preview in DOMContentLoaded and check if element exists
        document.addEventListener('DOMContentLoaded', function() {
            const photoInput = document.getElementById('photo_file');

            // Exit if element doesn't exist (form not displayed)
            if (!photoInput) return;

            photoInput.addEventListener('change', function (event) {
                const file = event.target.files[0];

                // Kiểm tra nếu không có file
                if (!file) {
                    // Clear preview and hidden input
                    document.getElementById('preview_img').style.display = 'none';
                    document.getElementById('resized_image_data').value = '';
                    return;
                }

                // Đọc và hiển thị ảnh
                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = document.getElementById('preview_img');
                    img.src = e.target.result;
                    img.style.display = 'block';

                    // LUÔN resize ảnh (hoặc convert sang base64 nếu nhỏ)
                    // Resize ảnh nếu kích thước lớn hơn 1MB, ngược lại giữ nguyên
                    if (file.size > 1024 * 1024) {
                        console.log('File > 1MB, resizing...');
                        resizeImage(e.target.result, img);
                    } else {
                        console.log('File <= 1MB, using original as base64...');
                        // Vẫn cần set giá trị cho resized_image_data với ảnh gốc
                        document.getElementById('resized_image_data').value = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
            });
        });

        function resizeImage(dataUrl, imgElement) {
            const img = new Image();
            img.onload = function () {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                const MAX_WIDTH = 800;  // Đặt kích thước tối đa của ảnh, có thể thay đổi

// Tính toán kích thước mới
                let width = img.width;
                let height = img.height;
                if (width > MAX_WIDTH) {
                    height *= MAX_WIDTH / width;
                    width = MAX_WIDTH;
                }

// Resize và lấy dữ liệu mới
                canvas.width = width;
                canvas.height = height;
                ctx.drawImage(img, 0, 0, width, height);

// Chuyển canvas thành URL mới cho ảnh đã resize
                const resizedDataUrl = canvas.toDataURL('image/jpeg', 0.7);  // Giảm chất lượng nếu cần thiết

// Hiển thị ảnh đã resize
                imgElement.src = resizedDataUrl;
                document.getElementById('resized_image_data').value = resizedDataUrl;
            };
            img.src = dataUrl;
        }
    </script>

    <!-- Choices.js JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/choices.js/10.2.0/choices.min.js"></script>

    <script>

        document.addEventListener('DOMContentLoaded', function() {


        });

        // Add some custom styling after initialization
        document.addEventListener('DOMContentLoaded', function() {
            // Focus styling
            const choicesContainer = document.querySelector('.choices');
            if (choicesContainer) {
                choicesContainer.addEventListener('focus', function() {
                    this.style.boxShadow = '0 0 0 3px rgba(0,123,255,0.25)';
                });
                choicesContainer.addEventListener('blur', function() {
                    this.style.boxShadow = 'none';
                });
            }



            //Nếu có #bankSelect thì mới chạy
            if (document.getElementById('bankSelect')) {
                // Initialize Choices.js
                const choices = new Choices('#bankSelect', {
                    searchEnabled: true,
                    searchChoices: true,
                    searchFloor: 1,
                    searchResultLimit: 10,
                    position: 'bottom',
                    allowHTML: false,
                    placeholder: true,
                    placeholderValue: 'Chọn hoặc gõ tên ngân hàng...',
                    searchPlaceholderValue: 'Gõ để tìm kiếm...',
                    noResultsText: 'Không tìm thấy ngân hàng nào',
                    noChoicesText: 'Không có lựa chọn nào',
                    itemSelectText: 'Nhấn để chọn',
                    removeItemButton: true,
                    shouldSort: false,
                    duplicateItemsAllowed: false,
                    paste: false,
                    addItems: true,
                    addItemFilter: null,
                    customAddItemText: 'Chỉ có thể chọn từ danh sách có sẵn',
                });

                // Handle selection change
                document.getElementById('bankSelect').addEventListener('change', function (event) {
                    const bankInfo = document.getElementById('bankInfo');
                    const selectedValue = event.target.value;

                    if (selectedValue === '') {
                        bankInfo.style.display = 'none';
                        return;
                    }

                    const info = selectedValue.split('|');
                    document.getElementById('bank_name_text').value = selectedValue
                    document.getElementById('bank_name_text').setAttribute('value', selectedValue);

                    console.log(" bank_name_text: " + document.getElementById('bank_name_text').value);

                    // document.getElementById('fullName').textContent = info[1];
                    // document.getElementById('englishName').textContent = info[2];

                    // Format category display
                    // const categoryMap = {
                    //     'TMCP': 'Thương mại cổ phần',
                    //     'TMNN': 'Thương mại một thành viên',
                    //     'NHCS': 'Ngân hàng chính sách',
                    //     '100% NN': '100% vốn nước ngoài',
                    //     'NHLD': 'Ngân hàng liên doanh',
                    //     'NHHTX': 'Ngân hàng hợp tác xã'
                    // };

                    // document.getElementById('category').textContent = categoryMap[info[3]] || info[3];
                    // bankInfo.style.display = 'block';
                });
            }

        });
    </script>

    <!-- LocalStorage Management for Form Inputs -->
    <script>
        /**
         * LocalStorage Manager cho form input values
         * Hiển thị dropdown các giá trị cũ khi focus
         */
        class FormLocalStorage {
            constructor(formId = 'formUpload', storagePrefix = 'event_reg_') {
                this.formId = formId;
                this.storagePrefix = storagePrefix;
                this.form = document.getElementById(formId);
                this.datalistContainer = null;

                if (!this.form) return;

                this.init();
            }

            init() {
                // Attach listeners
                this.attachChangeListeners();

                // Create container cho datalist
                this.createDatalistContainer();
            }

            /**
             * Tạo container cho datalist dropdown
             */
            createDatalistContainer() {
                if (document.getElementById('formDatalistContainer')) return;

                const container = document.createElement('div');
                container.id = 'formDatalistContainer';
                container.style.cssText = `
                    position: fixed;
                    display: none;
                    background: white;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.15);
                    z-index: 10000;
                    max-height: 250px;
                    overflow-y: auto;
                    min-width: 250px;
                `;
                document.body.appendChild(container);
                this.datalistContainer = container;
            }

            /**
             * Gắn event listener cho tất cả input để auto-save + show dropdown
             */
            attachChangeListeners() {
                const inputs = this.form.querySelectorAll(
                    'input[type="text"], input[type="email"], input[type="tel"], input[type="number"]'
                );

                inputs.forEach(input => {
                    // Save on input
                    input.addEventListener('input', (e) => {
                        this.saveFieldValue(input);
                    });

                    // Show dropdown on focus
                    input.addEventListener('focus', (e) => {
                        this.showHistoryDropdown(input);
                    });

                    // Hide dropdown on blur
                    input.addEventListener('blur', (e) => {
                        setTimeout(() => {
                            this.hideHistoryDropdown();
                        }, 200);
                    });

                    // Handle arrow key navigation
                    input.addEventListener('keydown', (e) => {
                        this.handleKeyNavigation(e);
                    });
                });
            }

            /**
             * Save một field vào LocalStorage + history
             */
            saveFieldValue(input) {
                if (!input.id) return;

                const key = this.storagePrefix + input.id;
                const value = input.value.trim();

                if (value) {
                    // Add to history
                    this.addToHistory(key, value);
                    console.log(`✅ Saved: ${input.id} = ${value}`);
                } else {
                    localStorage.removeItem(key);
                }
            }

            /**
             * Thêm giá trị vào history
             */
            addToHistory(key, value) {
                const historyKey = key + '_history';
                let history = JSON.parse(localStorage.getItem(historyKey) || '[]');

                // Loại bỏ duplicate
                history = history.filter(v => v !== value);

                // Thêm vào đầu (mới nhất trước)
                history.unshift(value);

                // Giữ tối đa 10 items
                history = history.slice(0, 10);

                localStorage.setItem(historyKey, JSON.stringify(history));
            }

            /**
             * Lấy lịch sử các giá trị đã nhập
             */
            getFieldHistory(key) {
                const historyKey = key + '_history';
                const history = JSON.parse(localStorage.getItem(historyKey) || '[]');
                return Array.isArray(history) ? history : [];
            }

            /**
             * Hiển thị dropdown với history khi focus
             */
            showHistoryDropdown(input) {
                if (!input.id) return;

                const key = this.storagePrefix + input.id;
                const history = this.getFieldHistory(key);

                if (history.length === 0) {
                    this.hideHistoryDropdown();
                    return;
                }

                // Create dropdown items
                const container = this.datalistContainer;
                container.innerHTML = '';

                history.forEach((value, index) => {
                    const item = document.createElement('div');
                    item.style.cssText = `
                        padding: 10px 15px;
                        cursor: pointer;
                        border-bottom: 1px solid #f0f0f0;
                        font-size: 14px;
                        transition: background 0.2s;
                    `;
                    item.textContent = value;
                    item.dataset.index = index;

                    item.addEventListener('mouseenter', () => {
                        item.style.background = '#f0f0f0';
                    });

                    item.addEventListener('mouseleave', () => {
                        item.style.background = 'white';
                    });

                    item.addEventListener('click', () => {
                        input.value = value;
                        input.focus();
                        this.hideHistoryDropdown();
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                    });

                    container.appendChild(item);
                });

                // Position dropdown
                const rect = input.getBoundingClientRect();
                container.style.left = rect.left + 'px';
                container.style.top = (rect.bottom + 5) + 'px';
                container.style.width = rect.width + 'px';
                container.style.display = 'block';

                console.log(`📋 Showing ${history.length} items for ${input.id}`);
            }

            /**
             * Ẩn dropdown
             */
            hideHistoryDropdown() {
                if (this.datalistContainer) {
                    this.datalistContainer.style.display = 'none';
                }
            }

            /**
             * Handle keyboard navigation
             */
            handleKeyNavigation(e) {
                if (e.key === 'Escape') {
                    this.hideHistoryDropdown();
                }
            }

            /**
             * Clear tất cả storage
             */
            clearStorage() {
                const keys = Object.keys(localStorage);
                keys.forEach(key => {
                    if (key.startsWith(this.storagePrefix)) {
                        localStorage.removeItem(key);
                    }
                });
                console.log('🗑️ Form data cleared from LocalStorage');
            }

            /**
             * Export dữ liệu dưới dạng JSON (debug)
             */
            exportData() {
                const data = {};
                const keys = Object.keys(localStorage);

                keys.forEach(key => {
                    if (key.startsWith(this.storagePrefix) && !key.includes('_history')) {
                        data[key.replace(this.storagePrefix, '')] = localStorage.getItem(key);
                    }
                });

                return data;
            }
        }

        // Initialize khi DOM ready
        document.addEventListener('DOMContentLoaded', function() {
            const formStorage = new FormLocalStorage('formUpload', 'event_reg_');

            // Expose để debug nếu cần
            window.formStorage = formStorage;

            // Nút để clear storage (optional)
            window.clearEventRegForm = function() {
                formStorage.clearStorage();
                location.reload();
            };
        });
    </script>

@endsection
