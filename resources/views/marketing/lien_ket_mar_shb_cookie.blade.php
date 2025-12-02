@extends(getLayoutNameMultiReturnDefaultIfNull())

@section("title")
    <?php

    $title = 'Ra nhập Liên kết marketing, mạng lưới: ';
    if ($uidLink = request('idf')) {
        $uidLink = qqgetIdFromRand_($uidLink);
        if ($obj = \App\Models\User::find($uidLink)) {
            $title .= ": " . $obj->name;
        }
    }
    echo $title;
    ?>
@endsection
@section("og_title")
    <?php
    echo $title
    ?>
@endsection
@section("css")

@endsection

@section("content")
    <div class="ladcont" data-code-pos="ppp1682132828815" style="min-height: 500px">
        <div class="container">
            <div class="jumbotron mt-4">
                <?php
                $uid = getCurrentUserId();
                $cname = \App\Models\NetworkMarketing_Meta::getCookieNameShb();
                $idParent0 = $idParent = request('idf');

                if(!$idParent)
                    die("Not found idlink!");

                $idParent = qqgetIdFromRand_($idParent);

                //Kiểm tra cookie trước đây nếu có, nếu có thì sẽ ưu tiên set cookie:
                if(isset($_COOKIE[$cname]) && $idPrCC0 = $idPrCC = $_COOKIE[$cname]){

                    try{
                        $idPrCC = qqgetIdFromRand_($idPrCC);
                    }
                    catch (\Throwable $e) { // For PHP 7
                    }
                    catch (\Exception $exception){
                    }
                    echo "<br/>\n Đã có cookie $idPrCC0";
                    $uParent = \App\Models\User::find($idPrCC);
                }else{
                    $uParent = \App\Models\User::find($idParent);
                    if(!$uParent){
                        loi("Not found user link $idParent0!");
                    }
                }
                //Kiểm tra valid parent:
                if(!$uParent)
                    loi("Not found user link $idParent0 (2)!");

                if(!$uid){
                    setcookie($cname, $idParent0,  time() + 3600 * 24 * 180, "/");
                    echo "<br/>\n Đã set cookie!";
                    return;
                }

                if($idParent == $uid){
                    $linkC =  \LadLib\Common\UrlHelper1::getFullUrl();
                    $linkC = explode("?", $linkC)[0];
                    echo "Đây là link kết nối của Bạn<br>Bạn hãy gửi link này để người khác đăng ký vào mạng của bạn!
<div style='color: dodgerblue'>
$linkC</div>";
                    goto _END;
                }
                else{
                    echo "<br/>\n Đây là link kết nối của $idParent0 ($uParent->email) ";
                }

                $objMyNet = \App\Models\NetworkMarketing::insertOrGetNetworkObj($uid);
                if($myParent = \App\Models\NetworkMarketing::getMyParent($uid)){
                    $prEnc = qqgetRandFromId_($myParent->id);
                    echo "<br/>\n Đã ra nhập mạng $prEnc, nên forward sang mạng lưới: ";
                    return;
                }else{
                    //Chưa có parent thì set
                    $objMyNet->setMyParent($idParent);
                    echo "<br/>\n Bạn đã ra nhập mạng: $idParent0!";
                }
                _END:
                ?>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <link rel="stylesheet" href="/assert/library_ex/jquery-ui/jquery-ui.css">
    <link rel="stylesheet" href="/vendor/bootstrap4/bootstrap.min.css">
    <link rel="stylesheet" href="/assert/library_ex/js/jquery-context-menu2/jquery.contextMenu.css">

    <link rel="stylesheet" href="/vendor/font-awesome/font-awesome4.css">
    <link rel="stylesheet" href="/vendor/toastr/toastr.min.css">

    <link rel="stylesheet" href="/vendor/lad_tree/clsTreeJs-v1.css?v=<?php echo filemtime(public_path().'/vendor/lad_tree/clsTreeJs-v1.css'); ?>">
    <link rel="stylesheet"
          href="/tool1/lad_tree_vn/clsTreeTopDown.css?v=1681812770">

    <script src="/vendor/jquery/jquery-3.6.0.js"></script>
    <script src="/vendor/jquery/jquery-ui-1.13.2.js"></script>
    <script src="/vendor/popper.min.js"></script>
    <script src="/vendor/bootstrap4/bootstrap.min.js"></script>
    <script src="/assert/library_ex/js/domti.js"></script>
    <script src="/assert/library_ex/js/fsv.js"></script>
    <script src="/assert/library_ex/js/svgpz.js"></script>
    <script src="/assert/library_ex/js/hmer.js"></script>
    <script src="/vendor/lazysizes.min.js"></script>
    <script src="/vendor/galaxy/lib_base.js?v=1"></script>
    <script src="/assert/library_ex/js/jquery-context-menu2/jquery.contextMenu.js"></script>
    <script src="/assert/library_ex/js/jquery-image-upload-resizer.js"></script>
    <script src="/assert/library_ex/jquery-ui/jquery.ui.position.js"></script>

    <script src="/vendor/toastr/toastr.min.js"></script>
    <script src="/admins/toast-show.js"></script>


@endsection
