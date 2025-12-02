<?php
/**
 *  Cơ chế check in sự kiện bằng QR code: qr-scaned-post.php?qrs= ....
 *  App sẽ reload lại qr-scaned-post.php (không tham số, khi đó nếu user có )
 */

//Test:
//1. Vao day de chon SK
// https://events.dav.edu.vn/tool1/_site/event_mng/qr-scaned-post.php
//2. Vao day de scan QR
//https://events.dav.edu.vn/tool1/_site/event_mng/qr-scaned-post.php?qrs=data=71|70&inputAllValx=71|2,1

//https://events.dav.edu.vn/tool1/_site/event_mng/qr-scaned-post.php?qrs=data=6|dungbkhn@yahoo.com&inputAllValx=6|2,1
//https://mytree.vn/tool1/_site/event_mng/qr-scaned-post.php?qrs=data=6|dungbkhn@yahoo.com&inputAllValx=6|2

use App\Models\ModelGlxBase;

error_reporting(E_ALL);
ini_set('display_errors', 1);
//$domain = $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'ncbd.mytree.vn';
$domain = $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'events.dav.edu.vn';
//require_once '/var/www/html/public/index.php';

require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

function ol1($str)
{
    $bname = basename(__FILE__);
    file_put_contents("/var/glx/weblog/$bname.log", date("Y-m-d H:i:s") . " # " . $str . "\n", FILE_APPEND);
}

ol1("request full: " . \LadLib\Common\UrlHelper1::getFullUrl());

if (!getCurrentUserId()) {
//    //redirect to login
//    echo "<br/>\n";
//    echo "<br/>\n";echo "<br/>\n";
//    bl(" <a href='/login'> Please login first! </a>");
//    return;
}

$evCurrentId = $optId = '';
$userNeedSign = $needSign = 0;

if ($inputAllValx = request('inputAllValx')) {
    if (strstr($inputAllValx, "|")) {
//        $inputAllValx = str_replace('"', '', $inputAllValx);
        list($evCurrentId, $optId) = explode("|", $inputAllValx);
//        echo "<b> Debug: $evId - $optId </b> <br>";
        $evCurrentId = trim($evCurrentId);
        $optIds = explode(',', trim($optId));
//        outputT("/var/glx/weblog/debug123.log", $inputAllValx);
    }
}

$uid = getCurrentUserId();

//selectValueEvId
//getCookie value here:
$thumb = $evCookie = null;
if ($selectValueEvId = ($_COOKIE["selectValueEvId"] ?? '')) {
    $evCookie = \App\Models\EventInfo::find($selectValueEvId);
//
//    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//    print_r($evCookie->toArray());
//    echo "</pre>";;

    if ($evCookie instanceof ModelGlxBase) ;

    $thumb = $evCookie->getThumbInImageList_CloudLink();
}

if (!$evCookie) {
//    echo "\n Bạn chưa chọn sự kiện?";
//    die();
}

$thisUserHaveNoSignature = 0;
if ($last_user_scan = ($_COOKIE["last_user_scan"] ?? '')) {

    ol1("_COOKIE Last user scan: $last_user_scan");
    //Kiểm tra xem có chữ ký chưa:
    $ue1 = \App\Models\EventUserInfo::find($last_user_scan);
    if ($ue1 && !$ue1->signature) {
        $thisUserHaveNoSignature = $ue1->id;
    }
}

$userNeedSign = \App\Models\EventUserInfo::find($thisUserHaveNoSignature);
//Nêu cần ký thì mới hiện bảng ký:

//User chưa ký bao giờ, và 1 trong 2 yeeu cầu cần ký
if ($thisUserHaveNoSignature)
    if ($evCookie->require_sign || $evCookie->require_sign_this_event) {
        $needSign = 1;
    }

//Yêu cầu ký tại sự kiện này, và sự kiện này user chưa ký:
if ($evCookie->require_sign_this_event ?? '') {
    if ($last_user_scan ?? '') {

        if ($uAndEv = \App\Models\EventAndUser::where('event_id', $evCookie->id)
            ->where('user_event_id', $last_user_scan)->first()) {
            if (!$uAndEv->signature) {
                $userNeedSign = \App\Models\EventUserInfo::find($last_user_scan);
                $needSign = 1;
            }
        }
    }
}

?>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


<link rel="stylesheet" href="/vendor/drawingboard.js/example/prism.css">
<link rel="stylesheet" href="/vendor/drawingboard.js/example/website.css">
<link rel="stylesheet" href="/vendor/drawingboard.js/css/drawingboard.css">

<link rel="stylesheet" href="/tool1/_site/event_mng/qr-scaned-post.css?v=2">

<script>

    function showAndroidToast(toastmsg) {
        if (typeof window.Android !== 'undefined')
            Android.showToast(toastmsg);
    }

    function clickShowCam() {
        if (typeof window.Android !== 'undefined')
            Android.showCameraJs('')
    }

    function clickHideCam() {
        if (typeof window.Android !== 'undefined')
            Android.hideCameraJs('')
    }

    function showAndroidDialog(dialogmsg) {
        if (typeof window.Android !== 'undefined')
            Android.showDialog(dialogmsg);
    }

    function moveToScreenTwo() {
        if (typeof window.Android !== 'undefined')
            Android.moveToNextScreen();
    }

</script>
<body style="">

<div style="text-align: center; position: fixed; top: 10px; font-size: 200%; right:10px; background-color: green; border-radius: 5px; padding: 2px 5px; color: #ccc" id="ms_user"></div>

<div style="
    background-color: <?php echo \App\Models\SiteMng::getInstance()->color1 ?>;
    color: white;
<?php

?>

    ">

    <div style="padding: 5px 10px; text-align: center; position: relative" class="logo" id="logo1">

        <div style="position: absolute; right: 10px; top: 10px;  display: inline-block">
            <i id="config_param" class="config fa fa-gear" style="color: #eee" onclick=""></i>
            <!--            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-->
            <!--            <i class="config fa fa-info" onclick="clickShowCam()"></i>-->
        </div>


        <a style="text-decoration: none; color: white"
           href="<?php echo \LadLib\Common\UrlHelper1::getUriWithoutParam() ?>">
            <!--            <b>-->
            <!--                <i>-->
            <!--            PLanEd-->
            <!--                </i>-->
            <!--            </b>-->
            <?php
            $img = \App\Models\SiteMng::getLogoIcon(3);
            if ($img) {
                echo "  <img src='$img' style='height: 50px; margin: 10px'>";
            }
            ?>
        </a>

    </div>

</div>

<?php

try {


function checkUserNeedSign($eventObj, $uid)
{
    if(!$user = \App\Models\EventUserInfo::find($uid))
        return 0;

    //Nêu cần ký thì mới hiện bảng ký:
    //User chưa ký bao giờ, và 1 trong 2 yeeu cầu cần ký
    if ($eventObj->require_sign) {
        if(!$user->signature)
            return 1;
    }
    //Yêu cầu ký tại sự kiện này, và sự kiện này user chưa ký:
    if ($eventObj->require_sign_this_event ?? '') {
        if ($uAndEv = \App\Models\EventAndUser::where('event_id', $eventObj->id)
            ->where('user_event_id', $uid)->first()) {
            //Neu user chua coo chu ky thi hien bang ky:
            if (!$uAndEv->signature) {
                return 1;
            }
        }
    }
    return 0;
}


//        echo "<pre style='font-size: 50%'>";
//        print_r(request()->all());
//        echo "</pre>";
//
//outputT("/var/glx/weblog/debug123.log", request()->fullUrl());

?>

<div id="optAll" style="display: none">
    <select class="sl1" name="" id="changeEvent">
        <option value="0"> - Chọn sự kiện -</option>
        <?php
        $allEv = \App\Models\EventInfo::orderBy('id', 'desc')->get();
        foreach ($allEv as $oneEv) {
            $sel = "";
            //if($evId && $evId == $oneEv->id)
            if ($evCurrentId && $evCurrentId == "$oneEv->id")
                $sel = "selected";
            echo "<option value='$oneEv->id' $sel> ($oneEv->id) $oneEv->name  </option>";
        }
        ?>
    </select>

    <!--<select  class="sl1 sl2" name="" id="changeEvOption">-->
    <!--    <option value="0"> - Chọn tham số - </option>-->
    <!--    --><?php
    //    for($i = 1; $i<=5; $i++){
    //        $sel ="";
    //        if($optId && $optId == "$i")
    //            $sel ="selected";
    //        echo "<option value='$i' $sel> Opt $i </option>";
    //    }
    //    ?>
    <!--</select>-->

    <div style="" class="opt_param">
        <?php

        for ($i = 1; $i <= 5; $i++) {
            echo "<div> Tham số $i: <input class='opt_text' data-id='$i' type='text' value=''/> <input type='checkbox' value='$i' name='changeEvOption'> </div>";
        }

        ?>
    </div>

</div>

<input type="hidden" id="inputAllValx" value="not_set">

<div
    id="main_zone"
    style="
        display: flex;
        justify-content: center;
        /*align-items: center;*/
        height: 100vh;

        /*background: linear-gradient(to bottom, rgba(0,0,0,0.6) 0%,rgba(0,0,0,0.6) 100%), url(https://ncbd.mytree.vn/test_cloud_file?fid=3136);*/
        background-image: url('<?php echo $thumb ?? '' ?>');
        background-color: hsla(0,0%,100%, <?php

    if ($evCookie && $evCookie->opacity)
        echo $evCookie->opacity / 100;
    else
        echo "0";
    ?>);
        background-blend-mode: overlay;
        background-size: cover;

        ">
    <div style="
    text-align: center;
    line-height: 1.6;
    font-size: 130%;
padding: 10px 10px 0px 10px;
    /*background-color: red;*/
">
        <?php

        $error = 0;
        $strSc = "";
        //inputAllValx

        //Chỗ này qrs = Full link từ QR code link:
        //qrs=https://events.dav.edu.vn/user-confirm-event/data/vm279495|7C3256475c5550595a5c724b535a5d5d1c515d5f
        if ($qrs = request('qrs')) {
            $inputAllValx = request('inputAllValx');
//        $qr = substr($qr, 0,10);
            $strSc = " <span style='font-size: 40%; color: #ccc'> <i> Scan QR Time: " . date('H:i:s') . " </i> </span>";

//            ol1(" QRS = $qrs");

            if (strpos($qrs, "data=") === false
                && strpos($qrs, "/data/") === false
                && strpos($qrs, "face_detect_x") === false
            ) {
                loi("QR code not valid, not data info!");
            }
//        re
//turn;
            $data = "";
            if (str_contains($qrs, 'data='))
                list($tmp, $data) = explode("data=", $qrs);
            elseif (str_contains($qrs, '/data/'))
                list($tmp, $data) = explode("/data/", $qrs);
            else
                //Truong hop Scan face
                $data = $qrs;

            list($evIdEncTmp, $emailEncOrUid) = explode("|", $data);

            $evId_InQrCode = $evIdEncTmp;
            if (!is_numeric($evIdEncTmp))
                $evId_InQrCode = qqgetIdFromRand_($evIdEncTmp);

            $emailOrUid = $emailEncOrUid;
//            $emailOrUid = dfh1b($emailEncOrUid);
            if (!str_contains($emailEncOrUid, '@'))
                if (!is_numeric($emailEncOrUid) || strlen($emailEncOrUid) >= 6) { // Sau nay sẽ bỏ số 6 này đi, vi eth1b mac dinh da co prefix = 'ms'
                    $tmpx = dfh1b($emailEncOrUid);
                    if (is_numeric($tmpx))
                        $emailOrUid = $tmpx;
                }

            if (!$evInQr = \App\Models\EventInfo::find($evId_InQrCode)) {
                loi("Not found event!");
            }

            if ($evCurrentId ?? '')
                if ($evId_InQrCode != $evCurrentId) {
                    //Kiểm tra xem sự kiện cha con, nếu QR $evId_InQrCode là cha của $evId đang chờ quét
                    if ($evId_InQrCode == \App\Models\EventInfo::find($evCurrentId)?->parent_id) {
                        //loi("Event is not valid! ($evId_InQrCode != $evId) </span>"
                    } else
                        loi("Event is not valid! ($evId_InQrCode != $evCurrentId) $evInQr->parent_id / $evId_InQrCode </span>");
                }


            if (!$evCookie) {
                loi("Bạn chưa chọn sự kiện? <br> <a href='/tool1/_site/event_mng/qr-scaned-post.php'> Trở lại </a>");
            }

            if ($evCookie->time_start || $evCookie->time_start_check_in) {
                $timeStart = min($evCookie->time_start, $evCookie->time_start_check_in);
                if ($timeStart > nowyh()) {
                    loi("Sự kiện chưa bắt đầu : <br>   $timeStart ($evCookie->id) ");
                }
            }

            if ($evCookie->time_end)
                if ($evCookie->time_end < nowyh()) {
                    loi("Sự kiện đã kết thúc vào lúc: <br> $evCookie->time_end ($evCookie->id)");
                }

            if (str_contains($emailOrUid, '@'))
                $ue = \App\Models\EventUserInfo::where('email', $emailOrUid)->first();
            else
                $ue = \App\Models\EventUserInfo::find($emailOrUid);

            if (!$ue) {
                loi("Not found this user! ($emailOrUid)");
            }

            $userIdEvent = $ue->id;
            ?>

            <script>
                document.cookie = "last_user_scan=<?php echo $userIdEvent ?>; expires=<?php
                    echo gmdate("D, d M Y H:i:s \U\T\C", time() + 600);
                    ?>; path=/";

                document.getElementById('main_zone').style.backgroundColor = "white";
                document.getElementById('main_zone').style.backgroundImage = "";

            </script>

        <?php
        //            setcookie("last_user_scan", $userIdEvent, time() + 3600, "/");

        //
        //        $ue = \App\Models\EventUserInfo::find(71);
        if ($ue instanceof \App\Models\EventUserInfo) ;
        if ($ue) {
        echo " <div data-code-pos='ppp17357894286151' style='color: #363636; background-color: '>";

        //                echo " <img src='/images/icon/success_qr.png' style='width: 40px ; margin: 10px'>  ";

        //                echo "\nXác nhận có mặt tại Sự kiện: <br> <b> $ev->name  </b>";
        if ($thumb = $ue->getThumbInImageList_CloudLink()) {
        ?>

            <div style='margin: 10px;'>
                <a href="/tool1/_site/event_mng/qr-scaned-post.php">
                <img style='; height: 70%; width: 100%; border-radius: 10px' src='<?php echo $thumb ?>'/>
                </a>
            </div>


        <?php

        }
        else
            //ko co anh thi margin
            echo " <div style='margin-top: 300px'> </div> ";

        $evAndU = \App\Models\EventAndUser::where('event_id', $evId_InQrCode)
            ->where('user_event_id', $ue->id)->first();

        echo " <div data-code-pos='ppp17357894465061' title='UID = $ue->id'>    " . $ue->getFullnameAndTitle() . "</div> ";

        ?>

            <script>
                document.getElementById('ms_user').textContent = 'ID: <?php echo $evAndU->id ?? "Not found" ?>'
            </script>
        <?php

        //Luon reload lai sau 2s neu quet đúng face, qr. Neu sai thi reload o dau?
//        if($needSign)
        if(!request('not_reload'))
        {
            ?>
            <script>
                setTimeout(function() {
                    window.location.href = '/tool1/_site/event_mng/qr-scaned-post.php';
                }, 4000);
            </script>
        <?php
        }

        if ($ue->organization) {
            echo " <div data-code-pos='ppp173575061' title='' style='font-size: 90%'>    " . $ue->organization . " </div> ";
        }

        if ($optIds ?? '') {
            foreach ($optIds as $optId) {
                $field = "extra_info$optId";
                $infoE = $evAndU->$field ?? '';
                if ($infoE)
                    //Thoong tin mo rong cho tung user:
                    echo " <div data-code-pos='ppp17292287277661' id='extra_info$optId' style='border: 0px solid red; font-size: 100%; font-weight: bold;  color: #363636; font-style: italic '> $infoE </div> ";
            }
        }


        if ($evU = \App\Models\EventAndUser::where('event_id', $evCurrentId)
            ->where('user_event_id', $ue->id)->first()) {
            $evU->addLog("QR scaned OK!");
            $evU->attend_at = nowyh();
//            $evU->update(['attend_at' => nowyh()]);
            $evU->save();
        } else {
            loi("User chưa đăng ký SK này? (Ev: $evCurrentId, UID $ue->id)!");
        }
        echo " <div>";
        ?>

            <script>
                // if (typeof window.Android !== 'undefined')
                //     Android.hideCameraJs('');
                document.getElementById('logo1').style.display = "none";
                // document.getElementById('main_zone').style.backgroundColor = "#6ac75f";
            </script>

        <?php

        }
        else {
            echo " <img src='/images/icon/not_success_qr.png' class='blink_me' style='width: 40px ; margin: 10px'>  ";
        }





        //Not qrs
        } else {
        if ($selectValueEvId) {
            $evCookie = \App\Models\EventInfo::find($selectValueEvId);

            $marginX = 130;
            if ($thisUserHaveNoSignature)
                $marginX = 50;

            echo "
<div class='titleEvent'  style='font-style: italic; font-size: 100%; margin-top: " . $marginX . "px'>
Welcome to
</div><b class='name_event' title='evid = $evCookie->id' style=''> $evCookie->name </b>
";
            if ($evCookie->name_sub) {
                echo "<div style='font-size: 80%; color: #363636; '> $evCookie->name_sub </div>";
            }
        }
        echo " <br> <h5 style='color: dodgerblue'> Please scan your QR code here  </h5> <span style='font-size: 50%'>" . nowyh() . "</span>";
        ?>
        <?php


        if ($needSign){

        ?>
        <div id="signature_zone">

            <?php

            if ($userNeedSign) {


                echo "<span style='' class='enter_sign'> " . $userNeedSign->getFullnameAndTitle() . "<br> Please enter your signature:  </span>";

                if (1)
                    if ($ue1 ?? '')

                        //Khong Phat audio o dau, ma phat ngay khi quet QR
                        if(0)
                        if ($ue1->language == 'vi') {
                            echo '<audio style="display: none" id="myAudio" src="ky_nhan_vn">vi|Xin Mời bạn ký nhận!</audio>';
                        } else
                            echo '<audio style="display: none" id="myAudio" src="ky_nhan_en">en|Please enter your signature!</audio>';
                ?>
                <!--                myAudio sẽ show ở 2 chỗ khác nhau, ở đây và ở dưới, thì dưới app nhận 2 mp3 va chay ca 2 MP3-->


                <!--                <script>-->
                <!--                    //Change sourc of audio link id myAudio-->
                <!--                    console.log("Change to ky nhan");-->
                <!--                    -->
                <!--                    document.getElementById('myAudio').src = "https://mytree.vn/public/music/ky_nhan_vn.mp3";-->
                <!--                </script>-->

                <div class="board" id="transparent-board"></div>

                <div class="buttonCtrl" style="">
                    <div id="status_save_sign"></div>
                    <button id="closeBoard" type="button" style="
                "> Close
                    </button>

                    <button id="saveSign" type="button" style=""> Save
                    </button>


                    <button id="clearBoard" type="button" style=""> Clear
                    </button>


                </div>

                </div>

                <?php
            }
        }


        }

        } catch (\Throwable $e) { // For PHP 7
            $error = 1;
            $ms = $e->getMessage();

            if(!($evInQr ?? '') && !($evCookie ?? '')){
                echo "<div class='not_valid' style='padding: 2px 20px; border-radius: 5px; background-color: red; color: white; border: 0px solid blue' > Bạn chưa chọn sự kiện! </div> ";
                return;
            }

            echo " <div class='not_valid' style='background-color: red; color: white; border: 0px solid blue'>";
            echo "
 <a href='/tool1/_site/event_mng/qr-scaned-post.php'>
 <img src='/images/icon/not_success_qr.png' class='blink_me' style='width: 40px ; margin: 10px'/>
 </a>
 ";
            echo "<br/>\n";
            echo "<h2>\n Access Denied: </h2> ";
            echo " <div style='font-size: 60%; color: #bbb'> $ms </div>";
            echo " </div> ";

            if ($qrs ?? '') {
                //Tao link để app nhận ra
                //Nếu không phải là face thi se co audio, neu khong thi qua nhieu audio keu len neu face khong dung
                if(!str_contains($qrs, 'face_detect_x'))
                    echo '<audio  style="display: none" id="myAudio" src="warning.mp3">warning.mp3</audio>';
            }
            ?>

            <script>

                //Delete cookie: $_COOKIE["last_user_scan"] vì user đã sai
                document.cookie = "last_user_scan=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";

                // if (typeof window.Android !== 'undefined')
                //     Android.hideCameraJs('hide1');
            </script>
            <script>

                // $("#main_zone").css("background-color", "red")
                document.getElementById('main_zone').style.backgroundColor = "red";
                document.getElementById('main_zone').style.alignItems = "center";
                document.getElementById('logo1').style.display = "none";

                // document.body.style.backgroundColor = "red";

            </script>
            <?php
        }

        echo "<div style='margin: 10px'> $strSc </div>  ";
        if ($qrs ?? '')
            if ($error == 0) {
                //Đoạn này luôn = 0: $userNeedSign || $needSign
//                if($userNeedSign || $needSign)
//                    echo '<audio id="myAudio" src="https://mytree.vn/public/music/ky_nhan_vn.mp3" ></audio>';
//                else
                //myAudio sẽ show ở 2 chỗ khác nhau, ở đây và ở trên, thì dưới app nhận 2 mp3 va chay ca 2 MP3

                //Se bat len 12.2.25:
                if(checkUserNeedSign($evCookie, $ue->id)){
                    if($ue ?? '')
                    if ($ue->language == 'vi') {
                        echo "<audio style='display: none' id='myAudio' src='ky_nhan_vn'>vi|Xin Mời bạn ký nhận! EID: $evCookie->id,  UID $ue->id</audio>";
                    } else
                        echo "<audio style='display: none' id='myAudio' src='ky_nhan_en'>en|Please enter your signature! EID: $evCookie->id,  UID $ue->id</audio>";

                }else
                    echo "<audio  style='display: none' id='myAudio' src='up.mp3'>up.mp3</audio>";
            }
        ?>
    </div>
</div>

</body>


<script>

    window.addEventListener('load', function () {

        var optTextValues = JSON.parse(localStorage.getItem('optTextValues')) || [];
        $(".opt_text").each(function (index) {
            this.value = optTextValues[index] || '';
        });

        var changeEvent = document.getElementById('changeEvent');

        var inputAllValx = document.getElementById('inputAllValx');


        var changeEvOption = document.getElementsByName('changeEvOption');
        document.getElementsByName('changeEvOption').forEach(function (el) {
            el.addEventListener('click', function () {
                changeEvOption.value = el.value;
                inputAllValx.value = changeEvent.value + "|" + changeEvOption.value;
                console.log(inputAllValx.value);
                // Lưu giá trị vào localStorage khi thay đổi

                let checkedValues = [];
                let checkedBoxes = document.querySelectorAll('input[name=changeEvOption]:checked');
                checkedBoxes.forEach((checkbox) => {
                    checkedValues.push(checkbox.value);
                });
                localStorage.setItem('select2Value', checkedValues);

                // localStorage.setItem('select2Value', changeEvOption.value);
            });

        });
        let checkedValues = localStorage.getItem('select2Value');
        let sl2 = localStorage.getItem('select2Value');
        console.log("SL2 = ", sl2, optTextValues, checkedValues);
        // $("input[name=changeEvOption][value=" + sl2 + "]").prop('checked', true);

        // Lấy danh sách các giá trị checkbox được check từ localStorage

        if (checkedValues) {
            // Chuyển chuỗi thành mảng
            checkedValues = checkedValues.split(',');

            // Duyệt qua mỗi giá trị trong mảng
            checkedValues.forEach((value) => {
                // Tìm checkbox có giá trị tương ứng và đánh dấu là đã check
                $(`input[name=changeEvOption][value=${value}]`).prop('checked', true);

                let valExtra = optTextValues[value - 1] ?? '';
                // $("#extra_info").html();
                $("#extra_info" + value).html(valExtra + $("#extra_info" + value).html());

            });
        }


        // Lấy giá trị từ localStorage và gán lại cho select.value và select2.value
        if (localStorage.getItem('selectValue')) {
            changeEvent.value = localStorage.getItem('selectValue');
            document.cookie = "selectValueEvId=" + changeEvent.value;
        }

        if (localStorage.getItem('select2Value')) {
            changeEvOption.value = localStorage.getItem('select2Value');
        }

        changeEvent.addEventListener('change', function () {
            inputAllValx.value = changeEvent.value + "|" + changeEvOption.value;
            console.log(inputAllValx.value);
            // Lưu giá trị vào localStorage khi thay đổi
            localStorage.setItem('selectValue', changeEvent.value);

            //set luon cookie o day:
            document.cookie = "selectValueEvId=" + changeEvent.value;
        });

        // changeEvOption.addEventListener('change', function () {
        //     inputAllValx.value = changeEvent.value + "|" + changeEvOption.value;
        //     console.log(inputAllValx.value);
        //     // Lưu giá trị vào localStorage khi thay đổi
        //     localStorage.setItem('select2Value', changeEvOption.value);
        // });

        inputAllValx.value = changeEvent.value + "|" + changeEvOption.value;

        document.getElementById('config_param').addEventListener('click', function () {

            $("#optAll").toggle();
        })

        // Save opt_text values to localStorage on keyup
        $(".opt_text").on('keyup', function () {
            var optTextValues = [];
            console.log("Change : ", this.value, this.dataset.id);
            $(".opt_text").each(function () {
                optTextValues.push(this.value);
            });

            localStorage.setItem('optTextValues', JSON.stringify(optTextValues));
        });

        $("#saveSign").on('click', function () {
            $("#status_save_sign").html('Saving....');
            // Lấy tham chiếu đến canvas
            var canvas = document.getElementsByClassName('drawing-board-canvas')[0];
            // Chuyển đổi canvas thành chuỗi base64
            var imageData = canvas.toDataURL('image/png');

            let user_token = '<?php echo \App\Models\User::where('email', env("AUTO_SET_ADMIN_EMAIL"))->first()->getJWTUserToken() ?>';

            // alert("COOKIE = " + document.cookie)
            // Chuyển đổi canvas thành Blob
            canvas.toBlob(function (blob) {
                // Tạo một đối tượng FormData để gửi dữ liệu tệp
                var formData = new FormData();
                formData.append('file_data', blob, 'signature-<?php echo($last_user_scan ?? '') ?>-<?php echo($evCookie->id ?? '') ?>.png');

                $.ajax({
                    url: 'https://events.dav.edu.vn/api/member-file/upload',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                    },
                    success: function (data) {
                        console.log(data);
                        var id = data.payload.id;

                        let evId = '<?php echo($evCookie->id ?? '') ?>';
                        let url = "/api/event-info/saveSignatureUser?uid=" + '<?php echo $last_user_scan ?>' + "&eventId=" + evId;

                        // $("#status_save_sign").html("URL = " + url);
                        // return;

                        console.log(" URL SIGN = ", url);

                        $.ajax({
                            url: url,
                            method: 'POST',
                            contentType: 'application/json',
                            data: JSON.stringify({signatureImgId: id}),
                            beforeSend: function (xhr) {
                                xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                            },
                            success: function (data) {
                                console.log(data);
                                if (data.code == 1) {
                                    $("#status_save_sign").html('Save signature successfully');
                                    $('#signature_zone').hide();
                                    clearBoard();
                                } else {
                                    $("#status_save_sign").html('*** Error in save signature0: ' + data.message);
                                }
                            },
                            error: function (error) {
                                $("#status_save_sign").html('*** Error Save signature Error1: ' + JSON.stringify(error).substring(0, 200));
                            }
                        });
                    },
                    error: function (error) {
                        $("#status_save_sign").html('*** Error Save signature Error2: ' + JSON.stringify(error).substring(0, 200));
                    }
                });

            });
        })


        //////////////////////////////////////

    });

</script>

<script src="/adminlte/plugins/jquery/jquery.min.js"></script>

<!-- jquery is required - zepto might do the trick too -->
<!--<script src="/vendor/drawingboard.js/bower_components/jquery/dist/jquery.min.js"></script>-->
<script src="/vendor/drawingboard.js/bower_components/simple-undo/lib/simple-undo.js"></script>

<!-- in a production environment, just include the minified script. It contains the board and the default controls (size, nav, colors, download): -->
<!--<script src="../dist/drawingboard.min.js"></script>-->

<script src="/vendor/drawingboard.js/js/utils.js"></script>
<script src="/vendor/drawingboard.js/js/board.js"></script>
<script src="/vendor/drawingboard.js/js/controls/control.js"></script>
<script src="/vendor/drawingboard.js/js/controls/color.js"></script>
<script src="/vendor/drawingboard.js/js/controls/drawingmode.js"></script>
<script src="/vendor/drawingboard.js/js/controls/navigation.js"></script>
<script src="/vendor/drawingboard.js/js/controls/size.js"></script>
<script src="/vendor/drawingboard.js/js/controls/download.js"></script>

<script>

    //the "filler" mode currently doesn't work with transparent boards...
    //keeping default controls, replacing the DrawingMode one with a filler-less version
    var transparentBoardControls = DrawingBoard.Board.defaultOpts.controls.slice();
    transparentBoardControls.splice(
        DrawingBoard.Board.defaultOpts.controls.indexOf('DrawingMode'), 1, {DrawingMode: {filler: false}});
    var transparentBoard = new DrawingBoard.Board('transparent-board', {
        size: 3,  // Đặt cỡ bút mặc định là 5
        color: "#00F", // Đặt màu mặc định là đen
        background: false,
        controls: transparentBoardControls
        // controls: [
        // ],
    });

    function clearBoard() {
        console.log("Clear...");
        document.getElementsByClassName('drawing-board-control-navigation-reset')[0]?.click();
    }

    $(function () {
        $('#closeBoard').on('click', function () {
            $('#signature_zone').hide();
        });
        $('#clearBoard').on('click', function () {
            clearBoard();
        });

        clearBoard();
    });


</script>


</html>
